<?php

namespace App\Services;

use App\Events\NewMemberRegistered;
use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Support\EmailTemplateTokens;
use App\Services\Notifications\NotificationOrchestrator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class NewMemberRegistrationService
{
    public function __construct(
        private readonly MemberUplineService $memberUpline,
        private readonly DownlineHierarchyService $downlineHierarchy,
        private readonly NotificationOrchestrator $notifications,
    ) {}

    public function process(User $member): void
    {
        $member->loadMissing(['sponsor', 'team', 'profile']);

        $sponsor = $member->sponsor;
        $agencyOwner = $this->memberUpline->agencyOwner($member);

        $this->sendWelcomeEmails($member, $sponsor, $agencyOwner);
        $this->sendCfmAssignmentNotifications($member, $sponsor, $agencyOwner);

        $this->downlineHierarchy->rebuild();
    }

    private function sendWelcomeEmails(User $member, ?User $sponsor, ?User $agencyOwner): void
    {
        $this->sendTemplatedEmail('new_member_welcome', $member->email, $this->tokens($member, $sponsor, $agencyOwner));

        if ($sponsor) {
            $this->sendTemplatedEmail('sponsor_new_member_welcome', $sponsor->email, $this->tokens($member, $sponsor, $agencyOwner));
        }

        if ($agencyOwner) {
            $this->sendTemplatedEmail('agency_owner_new_member_welcome', $agencyOwner->email, $this->tokens($member, $sponsor, $agencyOwner));
        }
    }

    private function sendCfmAssignmentNotifications(User $member, ?User $sponsor, ?User $agencyOwner): void
    {
        if ($agencyOwner) {
            $this->notifications->dispatch('assign_cfm_reminder', [
                'queue' => true,
                'recipients' => [$agencyOwner->id],
                'module' => 'registration',
                'priority' => 'high',
                'related' => ['type' => User::class, 'id' => $member->id],
                'related_user_id' => $member->id,
                'template_data' => [
                    'member_name' => $member->name,
                ],
                'payload' => [
                    'category' => 'Mentor Assignment',
                    'member_id' => $member->id,
                    'member_name' => $member->name,
                ],
                'action_link' => [
                    'route' => 'team.cfms',
                    'label' => 'Assign CFM',
                ],
            ]);
        }

        if ($sponsor && $agencyOwner?->id !== $sponsor->id) {
            $this->notifications->dispatch('recommend_cfm_reminder', [
                'queue' => true,
                'recipients' => [$sponsor->id],
                'module' => 'registration',
                'priority' => 'medium',
                'related' => ['type' => User::class, 'id' => $member->id],
                'related_user_id' => $member->id,
                'template_data' => [
                    'member_name' => $member->name,
                    'agency_owner_name' => $agencyOwner?->name ?? 'your agency owner',
                ],
                'payload' => [
                    'category' => 'Mentor Assignment',
                    'member_id' => $member->id,
                    'member_name' => $member->name,
                    'agency_owner_name' => $agencyOwner?->name ?? 'your agency owner',
                ],
                'action_link' => [
                    'route' => 'team.member',
                    'params' => ['user' => $member->id],
                    'label' => 'View member',
                ],
            ]);
        }
    }

    private function sendTemplatedEmail(string $templateKey, string $recipientEmail, array $tokens): void
    {
        $template = EmailTemplate::query()
            ->where('key', $templateKey)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            $message = "Active email template [{$templateKey}] is missing; welcome email to [{$recipientEmail}] was not sent.";

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

    private function tokens(User $member, ?User $sponsor, ?User $agencyOwner): array
    {
        return EmailTemplateTokens::merge(array_merge(
            EmailTemplateTokens::forMember($member),
            [
            'app_name' => config('app.name', 'EFGTrack'),
            'sponsor_name' => $sponsor?->name ?? 'your sponsor',
            'agency_owner_name' => $agencyOwner?->name ?? $this->memberUpline->agencyOwnerName($member),
            'dashboard_url' => route('dashboard'),
            'profile_url' => route('profile.edit'),
            'trigger' => NewMemberRegistered::TRIGGER,
        ]), route('dashboard', [], false));
    }
}
