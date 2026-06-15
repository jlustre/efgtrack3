<?php

namespace App\Services;

use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
use App\Models\MentorAssignment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class CfmAssignmentWorkflowService
{
    public function __construct(
        private readonly MemberUplineService $memberUpline,
    ) {}

    public function sendConfirmationRequest(MentorAssignment $assignment, bool $notifyCfm = true): void
    {
        if (! $notifyCfm || $assignment->status !== 'pending') {
            return;
        }

        $assignment->loadMissing(['mentor', 'apprentice.sponsor', 'assignedBy']);

        $this->sendTemplatedEmail(
            'cfm_assignment_confirmation_request',
            $assignment->mentor->email,
            $this->tokensFor($assignment, [
                'confirmation_url' => $this->confirmationUrl($assignment),
                'cfm_portal_url' => route('cfm.portal', [], false),
            ]),
        );
    }

    public function confirmAssignment(MentorAssignment $assignment, User $actingCfm): MentorAssignment
    {
        $assignment->loadMissing(['mentor', 'apprentice.sponsor', 'assignedBy']);

        if ($assignment->status !== 'pending') {
            throw ValidationException::withMessages([
                'assignment' => 'This assignment is no longer awaiting confirmation.',
            ]);
        }

        if ($assignment->mentor_id !== $actingCfm->id && ! $actingCfm->hasAnyRole(['super-admin', 'admin'])) {
            throw ValidationException::withMessages([
                'assignment' => 'You are not authorized to confirm this assignment.',
            ]);
        }

        if ($assignment->apprentice->mentor_id) {
            throw ValidationException::withMessages([
                'assignment' => 'This associate already has an active CFM.',
            ]);
        }

        return DB::transaction(function () use ($assignment): MentorAssignment {
            $assignment->update([
                'status' => 'active',
                'confirmed_at' => now(),
            ]);

            $assignment->apprentice->update([
                'mentor_id' => $assignment->mentor_id,
            ]);

            $this->sendConfirmationEmails($assignment->fresh(['mentor', 'apprentice.sponsor', 'assignedBy']));

            return $assignment;
        });
    }

    public function sendFirstContactEmail(MentorAssignment $assignment, User $actingCfm): void
    {
        $assignment->loadMissing(['mentor', 'apprentice.sponsor']);

        if ($assignment->status !== 'active' || $assignment->mentor_id !== $actingCfm->id) {
            throw ValidationException::withMessages([
                'assignment' => 'You can only send a first contact email for your active trainees.',
            ]);
        }

        if ($assignment->first_contact_sent_at) {
            throw ValidationException::withMessages([
                'assignment' => 'The first contact email was already sent to this trainee.',
            ]);
        }

        $this->sendTemplatedEmail(
            'cfm_first_contact_member',
            $assignment->apprentice->email,
            $this->tokensFor($assignment, [
                'cfm_portal_url' => route('cfm.portal', [], false),
                'dashboard_url' => route('dashboard', [], false),
            ]),
        );

        $assignment->update(['first_contact_sent_at' => now()]);
    }

    private function sendConfirmationEmails(MentorAssignment $assignment): void
    {
        $member = $assignment->apprentice;
        $sponsor = $member->sponsor;
        $cfm = $assignment->mentor;

        $baseTokens = $this->tokensFor($assignment, [
            'cfm_portal_url' => route('cfm.portal', [], false),
            'dashboard_url' => route('dashboard', [], false),
        ]);

        $this->sendTemplatedEmail('cfm_assignment_confirmed_member', $member->email, $baseTokens);

        if ($sponsor) {
            $this->sendTemplatedEmail('cfm_assignment_confirmed_sponsor', $sponsor->email, $baseTokens);
        }

        $this->sendTemplatedEmail('cfm_assignment_confirmed_cfm', $cfm->email, array_merge($baseTokens, [
            'first_contact_url' => route('cfm.portal', [], false),
        ]));
    }

    public function confirmationUrl(MentorAssignment $assignment): string
    {
        return URL::signedRoute('cfm.assignments.confirm', [
            'assignment' => $assignment->id,
        ]);
    }

    private function tokensFor(MentorAssignment $assignment, array $extra = []): array
    {
        $member = $assignment->apprentice;
        $cfm = $assignment->mentor;
        $sponsor = $member->sponsor;
        $agencyOwner = $this->memberUpline->agencyOwner($member);

        return array_merge([
            'app_name' => config('app.name', 'EFGTrack'),
            'member_name' => $member->name,
            'member_email' => $member->email,
            'cfm_name' => $cfm->name,
            'cfm_email' => $cfm->email,
            'sponsor_name' => $sponsor?->name ?? 'the sponsor',
            'agency_owner_name' => $agencyOwner?->name ?? $this->memberUpline->agencyOwnerName($member),
            'assigned_by_name' => $assignment->assignedBy?->name ?? 'Agency leadership',
        ], $extra);
    }

    private function sendTemplatedEmail(string $templateKey, string $recipientEmail, array $tokens): void
    {
        $template = EmailTemplate::query()
            ->where('key', $templateKey)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            $message = "Active email template [{$templateKey}] is missing; email to [{$recipientEmail}] was not sent.";

            Log::warning($message, [
                'template_key' => $templateKey,
                'recipient_email' => $recipientEmail,
            ]);

            if (app()->environment(['local', 'testing'])) {
                throw new RuntimeException($message.' Run: php artisan db:seed --class=EmailTemplateSeeder');
            }

            return;
        }

        Mail::to($recipientEmail)->send(new TemplatedMail(
            $template->renderSubject($tokens),
            $template->renderBody($tokens),
        ));
    }
}
