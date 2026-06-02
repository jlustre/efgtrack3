<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Mail\InvitationLinkMail;
use App\Models\EmailTemplate;
use App\Models\RegistrationInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load([
            'profile',
            'rank',
            'team',
            'sponsor',
            'registrationInvitations' => fn ($query) => $query->active()->latest()->limit(5),
        ]);

        $invitationTemplate = EmailTemplate::where('key', 'member_invitation')
            ->where('is_active', true)
            ->first();
        $invitationEmails = $user->registrationInvitations
            ->mapWithKeys(fn (RegistrationInvitation $invitation) => [
                $invitation->id => $this->renderInvitationEmail($invitation, $invitationTemplate),
            ]);

        return view('profile.edit', [
            'user' => $user,
            'recentInvitations' => $user->registrationInvitations,
            'invitationTemplate' => $invitationTemplate,
            'invitationEmails' => $invitationEmails,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'phone' => $validated['phone'] ?? null,
                'province' => $validated['province'] ?? null,
                'city' => $validated['city'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
                'efg_associate_id' => $validated['efg_associate_id'] ?? null,
                'bio' => $validated['bio'] ?? null,
            ]
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function createInvitation(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],
        ]);

        $this->ensureEmailCanReceiveInvitation($validated['email'] ?? null);

        $invitation = RegistrationInvitation::create([
            'sponsor_id' => $request->user()->id,
            'code' => RegistrationInvitation::generateCode(),
            'email' => $validated['email'] ?? null,
            'role_name' => 'member',
            'max_uses' => 1,
            'uses_count' => 0,
            'expires_at' => now()->addDays(14),
        ]);

        return Redirect::route('profile.edit')
            ->with('status', 'invitation-created')
            ->with('invitation_id', $invitation->id)
            ->with('invitation_url', $invitation->invitationUrl());
    }

    public function sendInvitationEmail(Request $request, RegistrationInvitation $invitation): RedirectResponse
    {
        abort_unless($invitation->sponsor_id === $request->user()->id, 403);

        $validated = $request->validate([
            'recipient_email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $this->ensureEmailCanReceiveInvitation($validated['recipient_email'], $invitation);

        if (! str_contains($validated['message'], $invitation->invitationUrl())) {
            throw ValidationException::withMessages([
                'message' => 'The registration link must remain included in the email message.',
            ]);
        }

        Mail::to($validated['recipient_email'])
            ->send(new InvitationLinkMail(
                $validated['subject'],
                $validated['message'],
                $request->user()->name,
                $request->user()->email,
            ));

        $invitation->forceFill([
            'email' => $validated['recipient_email'],
            'last_emailed_at' => now(),
        ])->save();

        return Redirect::route('profile.edit')->with('status', 'invitation-email-sent');
    }

    public function destroyInvitation(Request $request, RegistrationInvitation $invitation): RedirectResponse
    {
        abort_unless($invitation->sponsor_id === $request->user()->id, 403);

        $invitation->forceFill([
            'revoked_at' => now(),
        ])->save();

        return Redirect::route('profile.edit')->with('status', 'invitation-deleted');
    }

    private function ensureEmailCanReceiveInvitation(?string $email, ?RegistrationInvitation $currentInvitation = null): void
    {
        if (! $email) {
            return;
        }

        if (\App\Models\User::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                $currentInvitation ? 'recipient_email' : 'email' => 'This email is already registered as an EFGTrack member.',
            ]);
        }

        $activeInvitationQuery = RegistrationInvitation::activeForEmail($email);

        if ($currentInvitation) {
            $activeInvitationQuery->whereKeyNot($currentInvitation->id);
        }

        if ($activeInvitationQuery->exists()) {
            throw ValidationException::withMessages([
                $currentInvitation ? 'recipient_email' : 'email' => 'An active registration link already exists for this email recipient.',
            ]);
        }
    }

    public function renderInvitationEmail(RegistrationInvitation $invitation, ?EmailTemplate $template = null): array
    {
        $template ??= EmailTemplate::where('key', 'member_invitation')
            ->where('is_active', true)
            ->first();

        $tokens = [
            'app_name' => config('app.name', 'EFGTrack'),
            'sponsor_name' => $invitation->sponsor?->name ?? 'An EFGTrack member',
            'registration_link' => $invitation->invitationUrl(),
            'registration_code' => $invitation->code,
            'expires_at' => $invitation->expires_at?->format('F j, Y') ?? 'the expiration date',
        ];

        return [
            'subject' => $template?->renderSubject($tokens) ?? 'You are invited to join '.config('app.name', 'EFGTrack'),
            'body' => $template?->renderBody($tokens) ?? $invitation->invitationUrl(),
        ];
    }

}
