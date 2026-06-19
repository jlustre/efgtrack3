<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UpdateProfileInsuranceLicensesRequest;
use App\Http\Requests\UpdateProfileInviteLinkRequest;
use App\Http\Requests\UpdateProfilePhotoRequest;
use App\Mail\InvitationLinkMail;
use App\Models\EmailTemplate;
use App\Models\RegistrationInvitation;
use App\Models\User;
use App\Services\DownlineHierarchyService;
use App\Services\MemberProfileTabsService;
use App\Services\MemberUplineService;
use App\Services\ProfileCompletionService;
use App\Services\ProfilePhotoService;
use App\Support\LocationOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly MemberProfileTabsService $memberProfileTabs,
        private readonly MemberUplineService $memberUpline,
        private readonly ProfilePhotoService $profilePhotos,
        private readonly DownlineHierarchyService $downlineHierarchy,
        private readonly ProfileCompletionService $profileCompletion,
    ) {}

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View|RedirectResponse
    {
        return $this->profilePage($request, $request->user(), isOwnProfile: true);
    }

    public function showMember(Request $request, User $user): View|RedirectResponse
    {
        abort_unless($this->downlineHierarchy->canViewMember($request->user(), $user), 403);

        return $this->profilePage($request, $user, isOwnProfile: false);
    }

    private function profilePage(Request $request, User $user, bool $isOwnProfile): View|RedirectResponse
    {
        if ($request->query('tab') === 'direct-recruits') {
            $redirectRoute = $isOwnProfile
                ? route('profile.edit', array_merge($request->except('tab'), ['tab' => 'recruits']))
                : route('team.member.profile', array_merge(['user' => $user], $request->except('tab'), ['tab' => 'recruits']));

            return Redirect::to($redirectRoute);
        }

        $user->unsetRelation('profile');

        $user->load([
            'profile',
            'rank',
            'team.owner',
            'team',
            'sponsor',
            'mentor',
        ]);

        $invitationTemplate = null;
        $invitationEmails = collect();
        $invitationHistory = collect();

        if ($isOwnProfile) {
            $invitationHistory = RegistrationInvitation::query()
                ->where('sponsor_id', $user->id)
                ->with('acceptedBy')
                ->latest()
                ->limit(100)
                ->get();

            $invitationTemplate = EmailTemplate::where('key', 'member_invitation')
                ->where('is_active', true)
                ->first();
            $invitationEmails = $invitationHistory
                ->filter(fn (RegistrationInvitation $invitation): bool => $invitation->isAvailable())
                ->mapWithKeys(fn (RegistrationInvitation $invitation) => [
                    $invitation->id => $this->renderInvitationEmail($invitation, $invitationTemplate),
                ]);
        }

        $viewer = $request->user();

        return view('profile.edit', [
            'user' => $user,
            'isOwnProfile' => $isOwnProfile,
            'viewer' => $viewer,
            'invitationHistory' => $invitationHistory,
            'invitationTemplate' => $invitationTemplate,
            'invitationEmails' => $invitationEmails,
            'memberTabs' => $this->memberProfileTabs->forUser($user),
            'profileCompletion' => $this->profileCompletion->snapshot($user),
            'profileContext' => [
                'readonly' => $this->memberUpline->contextFor($user),
                'locationOptions' => LocationOptions::forPortal(),
                'canViewSensitive' => $viewer->can('viewSensitive', $user),
            ],
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

        $profileAttributes = $this->profileAttributesFromRequest($request, $validated);

        if ($profileAttributes !== []) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileAttributes,
            );
        }

        $redirectRoute = $request->input('redirect_to') === 'dashboard'
            ? route('dashboard')
            : route('profile.edit', ['tab' => 'profile']);

        return Redirect::to($redirectRoute)->with('profile_feedback', [
            'type' => 'success',
            'message' => 'Your profile was updated successfully.',
        ]);
    }

    public function updateInsuranceLicenses(UpdateProfileInsuranceLicensesRequest $request): RedirectResponse
    {
        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['insurance_licenses' => $request->normalizedInsuranceLicenses()],
        );

        return Redirect::route('profile.edit', ['tab' => 'licenses'])->with('licenses_feedback', [
            'type' => 'success',
            'message' => 'Your insurance licenses were saved.',
        ]);
    }

    public function updateInviteLink(UpdateProfileInviteLinkRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'efg_associate_id' => filled($validated['efg_associate_id'] ?? null)
                    ? $validated['efg_associate_id']
                    : null,
                'efg_invite_link' => filled($validated['efg_invite_link'] ?? null)
                    ? $validated['efg_invite_link']
                    : null,
            ],
        );

        return Redirect::route('profile.edit')
            ->with('status', 'efg-invite-link-saved')
            ->with('efg_details_feedback', [
                'type' => 'success',
                'message' => 'Your EFG details were saved.',
            ]);
    }

    public function updatePhoto(UpdateProfilePhotoRequest $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $profile = $this->profilePhotos->update($user, $request->file('photo'));

        $user->unsetRelation('profile');
        $user->setRelation('profile', $profile);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your profile photo was updated.',
                'photo_url' => $user->profilePhotoUrl(),
                'profile_completion' => $this->profileCompletion->snapshot($user),
            ]);
        }

        $redirectRoute = $request->input('redirect_to') === 'dashboard'
            ? route('dashboard')
            : route('profile.edit', ['tab' => 'profile']);

        $redirect = Redirect::to($redirectRoute)->with('profile_feedback', [
            'type' => 'success',
            'message' => 'Your profile photo was updated.',
        ]);

        if ($request->input('redirect_to') === 'dashboard') {
            $redirect->with('show_profile_completion_modal', true);
        }

        return $redirect;
    }

    public function destroyPhoto(Request $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $this->profilePhotos->delete($user);

        $user->unsetRelation('profile');
        $user->load('profile');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your profile photo was removed.',
                'photo_url' => null,
                'profile_completion' => $this->profileCompletion->snapshot($user),
            ]);
        }

        $redirectRoute = $request->input('redirect_to') === 'dashboard'
            ? route('dashboard')
            : route('profile.edit', ['tab' => 'profile']);

        $redirect = Redirect::to($redirectRoute)->with('profile_feedback', [
            'type' => 'success',
            'message' => 'Your profile photo was removed.',
        ]);

        if ($request->input('redirect_to') === 'dashboard') {
            $redirect->with('show_profile_completion_modal', true);
        }

        return $redirect;
    }

    public function createInvitation(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->is_active, 403, 'Your account must be active to invite new members.');
        abort_unless(
            $user->hasAnyRole(['member', 'agency-owner', 'team-leader', 'certified-field-mentor']),
            403,
            'Only active EFGTrack members can send registration invitations.'
        );

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

        return Redirect::route('profile.edit', ['open_invitations' => 1])
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
            'message' => ['required', 'string', 'max:20000'],
        ]);

        $this->ensureEmailCanReceiveInvitation($validated['recipient_email'], $invitation);

        if (! $this->messageContainsRegistrationLink($validated['message'], $invitation->invitationUrl())) {
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

        return Redirect::route('profile.edit', ['open_invitations' => 1])
            ->with('status', 'invitation-email-sent');
    }

    public function destroyInvitation(Request $request, RegistrationInvitation $invitation): RedirectResponse
    {
        abort_unless($invitation->sponsor_id === $request->user()->id, 403);

        $invitation->forceFill([
            'revoked_at' => now(),
        ])->save();

        return Redirect::route('profile.edit', ['open_invitations' => 1])
            ->with('status', 'invitation-deleted');
    }

    private function ensureEmailCanReceiveInvitation(?string $email, ?RegistrationInvitation $currentInvitation = null): void
    {
        if (! $email) {
            return;
        }

        if (User::where('email', $email)->exists()) {
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

    private function messageContainsRegistrationLink(string $message, string $registrationUrl): bool
    {
        if (str_contains($message, $registrationUrl)) {
            return true;
        }

        $encodedUrl = htmlspecialchars($registrationUrl, ENT_QUOTES, 'UTF-8');

        return $encodedUrl !== $registrationUrl && str_contains($message, $encodedUrl);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function profileAttributesFromRequest(ProfileUpdateRequest $request, array $validated): array
    {
        $attributes = [];

        foreach ([
            'phone',
            'city',
            'country_id',
            'state_province_id',
            'timezone_id',
            'best_contact_time',
            'bio',
        ] as $field) {
            if ($request->has($field)) {
                $attributes[$field] = $validated[$field] ?? null;
            }
        }

        if ($request->has('efg_associate_id')) {
            $attributes['efg_associate_id'] = filled($validated['efg_associate_id'] ?? null)
                ? $validated['efg_associate_id']
                : null;
        }

        if ($request->has('efg_invite_link')) {
            $attributes['efg_invite_link'] = filled($validated['efg_invite_link'] ?? null)
                ? $validated['efg_invite_link']
                : null;
        }

        return $attributes;
    }
}
