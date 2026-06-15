<?php

namespace Tests\Feature;

use App\Mail\InvitationLinkMail;
use App\Models\Profile;
use App\Models\RegistrationInvitation;
use App\Models\User;
use App\Services\DownlineHierarchyService;
use App\Support\LocationOptions;
<<<<<<< HEAD
use Database\Seeders\CountrySeeder;
=======
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
use Database\Seeders\CfmTrainingModuleSeeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\FieldApprenticeshipProgramSeeder;
use Database\Seeders\OnboardingStepSeeder;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StateProvinceSeeder;
use Database\Seeders\TaskScenarioSeeder;
use Database\Seeders\TeamSeeder;
use Database\Seeders\TimezoneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response
            ->assertOk()
            ->assertDontSee('Delete Account')
            ->assertSee('Profile Details', false)
            ->assertSee('Update Profile Photo', false)
            ->assertSee('Onboarding', false)
            ->assertSee('Recruits', false)
            ->assertSee('Annual Premium', false)
            ->assertSee('Other Training', false)
            ->assertSee('Experior Invite Link', false)
            ->assertSee('Save Invite Link', false);
    }

    public function test_profile_page_shows_tab_listing_tables_with_seeded_data(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            RankSeeder::class,
            TeamSeeder::class,
            OnboardingStepSeeder::class,
            FieldApprenticeshipProgramSeeder::class,
            CfmTrainingModuleSeeder::class,
            TaskScenarioSeeder::class,
        ]);

        $cfm = User::where('email', 'cfm@efgtrack.com')->firstOrFail();

        $this->actingAs($cfm)
            ->get(route('profile.edit', ['tab' => 'profile']))
            ->assertOk()
            ->assertSee('Agency Owner', false)
            ->assertSee('Arielle Morgan', false)
            ->assertSee('Best Contact Time', false)
            ->assertDontSee('Member Information', false);

        $this->actingAs($cfm)
            ->get(route('profile.edit', ['tab' => 'onboarding']))
            ->assertOk()
            ->assertSee('Onboarding Checklist', false)
            ->assertSee('Field Apprenticeship Program', false);
    }

    public function test_recruits_tab_shows_levels_and_total_with_search_and_filters(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            RankSeeder::class,
            CountrySeeder::class,
            StateProvinceSeeder::class,
<<<<<<< HEAD
            TimezoneSeeder::class,
=======
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
            OnboardingStepSeeder::class,
            FieldApprenticeshipProgramSeeder::class,
            CfmTrainingModuleSeeder::class,
        ]);

        $sponsor = User::factory()->create(['name' => 'Tab Sponsor']);
        $levelOne = User::factory()->create([
            'name' => 'Recruit Level One',
            'email' => 'recruit.level1@example.com',
            'sponsor_id' => $sponsor->id,
            'is_active' => true,
        ]);
        $levelOne->assignRole('certified-field-mentor');
<<<<<<< HEAD
        Profile::query()->create(LocationOptions::profileAttributesForStorage([
            'user_id' => $levelOne->id,
            'phone' => '+1 416-555-0100',
            'province' => 'Ontario',
            'country' => 'Canada',
        ]));
=======
        Profile::query()->create(array_merge([
            'user_id' => $levelOne->id,
            'phone' => '+1 416-555-0100',
        ], LocationOptions::profileLocationIds('Canada', 'Ontario')));
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
        User::factory()->create([
            'name' => 'Recruit Level Two',
            'email' => 'recruit.level2@example.com',
            'sponsor_id' => $levelOne->id,
            'is_active' => true,
        ]);

        app(DownlineHierarchyService::class)->rebuild();

        $this->actingAs($sponsor)
            ->get(route('profile.edit', ['tab' => 'recruits']))
            ->assertOk()
            ->assertSee('Total recruits: 2', false)
            ->assertSee('1 at Level 1', false)
            ->assertSee('Level', false)
            ->assertSee('Member', false)
            ->assertSee('Role', false)
            ->assertSee('Location', false)
            ->assertSee('Recruit Level One', false)
            ->assertSee('recruit.level1@example.com', false)
            ->assertSee('+1 416-555-0100', false)
            ->assertSee('Recruit Level Two', false)
            ->assertSee('CFM', false)
            ->assertSee('Ontario', false)
            ->assertSee('Search by name, email, phone, role, rank, sponsor', false)
            ->assertSee('All levels', false)
            ->assertSee('All roles', false)
            ->assertSee('All provinces', false)
            ->assertSee('Clear filters', false);

        $this->actingAs($sponsor)
            ->get(route('profile.edit', ['tab' => 'direct-recruits']))
            ->assertRedirect(route('profile.edit', ['tab' => 'recruits']));

        $this->actingAs($sponsor)
            ->get(route('profile.edit', ['tab' => 'annual-premium']))
            ->assertOk()
            ->assertSee('Search by source, description, period', false)
            ->assertSee('All sources', false)
            ->assertSee('Filtered total', false);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $this->seed([
            CountrySeeder::class,
            StateProvinceSeeder::class,
            TimezoneSeeder::class,
        ]);

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '555-123-4567',
                'state_province_id' => LocationOptions::resolveStateProvinceId('Canada', 'Ontario'),
                'city' => 'Toronto',
                'country_id' => LocationOptions::resolveCountryId('Canada'),
                'timezone_id' => LocationOptions::resolveTimezoneId('Canada Eastern Time'),
                'best_contact_time' => 'Morning (8am – 12pm)',
                'license_number' => 'LIC-123',
                'efg_associate_id' => 'EFG-2001',
                'efg_invite_link' => 'https://efg.example.com/invite/2001',
                'bio' => 'Building a strong financial services team.',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit', ['tab' => 'profile']))
            ->assertSessionHas('profile_feedback', fn (array $feedback) => $feedback['type'] === 'success');

        $this->actingAs($user)
            ->get(route('profile.edit', ['tab' => 'profile']))
            ->assertOk()
            ->assertSee('Profile saved', false)
            ->assertSee('Your profile was updated successfully.', false);

        $user->refresh()->load('profile.stateProvince', 'profile.countryRecord', 'profile.timezoneRecord');

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
        $this->assertSame('555-123-4567', $user->profile->phone);
        $this->assertSame('Ontario', $user->profile->province);
        $this->assertSame('Toronto', $user->profile->city);
        $this->assertSame('Canada', $user->profile->country);
        $this->assertSame('Canada Eastern Time', $user->profile->timezone);
        $this->assertSame('Morning (8am – 12pm)', $user->profile->best_contact_time);
        $this->assertSame('LIC-123', $user->profile->license_number);
        $this->assertSame('EFG-2001', $user->profile->efg_associate_id);
        $this->assertSame('https://efg.example.com/invite/2001', $user->profile->efg_invite_link);
    }

    public function test_profile_update_shows_validation_errors_on_profile_tab(): void
    {
        $this->seed([
            CountrySeeder::class,
            StateProvinceSeeder::class,
            TimezoneSeeder::class,
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('profile.edit', ['tab' => 'profile']))
            ->patch(route('profile.update'), [
                'name' => 'Test User',
                'email' => 'not-an-email',
                'country_id' => LocationOptions::resolveCountryId('Canada'),
                'state_province_id' => LocationOptions::resolveStateProvinceId('United States', 'California'),
                'timezone_id' => 999999,
            ])
            ->assertRedirect(route('profile.edit', ['tab' => 'profile']))
            ->assertSessionHas('profile_feedback', fn (array $feedback) => $feedback['type'] === 'error')
            ->assertSessionHasErrors(['email', 'state_province_id', 'timezone_id']);

        $this->actingAs($user)
            ->get(route('profile.edit', ['tab' => 'profile']))
            ->assertOk()
            ->assertSee('Could not save profile', false)
            ->assertSee('Save Profile', false);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit', ['tab' => 'profile']));

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_member_can_save_experior_invite_link(): void
    {
        $user = User::factory()->create();
        $inviteLink = 'https://experiorfinancial.com/invite/abc123';

        $response = $this
            ->actingAs($user)
            ->patch(route('profile.invite-link.update'), [
                'efg_associate_id' => 'EFG-2001',
                'efg_invite_link' => $inviteLink,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('status', 'efg-invite-link-saved');

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'efg_associate_id' => 'EFG-2001',
            'efg_invite_link' => $inviteLink,
        ]);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('EFG Details')
            ->assertSee($inviteLink, false);
    }

    public function test_experior_invite_link_must_be_a_valid_url(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('profile.edit'))
            ->patch(route('profile.invite-link.update'), [
                'efg_invite_link' => 'not-a-valid-url',
            ]);

        $response
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('efg_invite_link');

        $this->assertDatabaseMissing('profiles', [
            'user_id' => $user->id,
            'efg_invite_link' => 'not-a-valid-url',
        ]);
    }

    public function test_member_can_clear_experior_invite_link(): void
    {
        $user = User::factory()->create();
        Profile::query()->create([
            'user_id' => $user->id,
            'efg_invite_link' => 'https://experiorfinancial.com/invite/old',
        ]);

        $response = $this
            ->actingAs($user)
            ->patch(route('profile.invite-link.update'), [
                'efg_invite_link' => '',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit'));

        $this->assertNull($user->fresh()->profile->efg_invite_link);
    }

    public function test_member_can_create_invitation_link_from_profile(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        $response = $this
            ->actingAs($user)
            ->post('/profile/invitations', [
                'email' => 'invitee@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertDatabaseHas('registration_invitations', [
            'sponsor_id' => $user->id,
            'email' => 'invitee@example.com',
            'role_name' => 'member',
            'max_uses' => 1,
            'uses_count' => 0,
        ]);

        $this->assertNotNull(session('invitation_url'));
    }

    public function test_member_cannot_create_duplicate_active_invitation_for_same_email(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        RegistrationInvitation::factory()->for($user, 'sponsor')->create([
            'email' => 'invitee@example.com',
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/profile/invitations', [
                'email' => 'invitee@example.com',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_accepted_invitations_are_not_shown_on_profile(): void
    {
        $user = User::factory()->create();
        $acceptedUser = User::factory()->create(['email' => 'accepted@example.com']);
        $activeInvitation = RegistrationInvitation::factory()->for($user, 'sponsor')->create([
            'email' => 'active@example.com',
        ]);

        RegistrationInvitation::factory()->for($user, 'sponsor')->create([
            'email' => 'accepted@example.com',
            'accepted_by' => $acceptedUser->id,
            'accepted_at' => now(),
            'revoked_at' => now(),
            'uses_count' => 1,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response
            ->assertOk()
            ->assertSee($activeInvitation->code)
            ->assertSee('active@example.com')
            ->assertDontSee('accepted@example.com');
    }

    public function test_member_can_delete_active_invitation_and_reinvite_same_email(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        $invitation = RegistrationInvitation::factory()->for($user, 'sponsor')->create([
            'email' => 'invitee@example.com',
        ]);

        $deleteResponse = $this
            ->actingAs($user)
            ->delete(route('profile.invitations.destroy', $invitation));

        $deleteResponse
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($invitation->refresh()->revoked_at);

        $createResponse = $this
            ->actingAs($user)
            ->post('/profile/invitations', [
                'email' => 'invitee@example.com',
            ]);

        $createResponse
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertSame(2, RegistrationInvitation::where('email', 'invitee@example.com')->count());
    }

    public function test_member_cannot_invite_an_email_that_already_registered(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this
            ->actingAs($user)
            ->post('/profile/invitations', [
                'email' => 'existing@example.com',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_member_can_send_invitation_email_after_previewing_message(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $invitation = RegistrationInvitation::factory()->for($user, 'sponsor')->create([
            'email' => 'invitee@example.com',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('profile.invitations.send', $invitation), [
                'recipient_email' => 'invitee@example.com',
                'subject' => 'Join EFGTrack',
                'message' => "Please register here:\n".$invitation->invitationUrl(),
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        Mail::assertSent(InvitationLinkMail::class, function (InvitationLinkMail $mail) use ($user) {
            return $mail->senderName === $user->name
                && $mail->senderEmail === $user->email;
        });

        $this->assertNotNull($invitation->refresh()->last_emailed_at);
    }

    public function test_member_can_send_invitation_email_with_html_message_body(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $invitation = RegistrationInvitation::factory()->for($user, 'sponsor')->create([
            'email' => 'invitee@example.com',
        ]);

        $url = $invitation->invitationUrl();
        $htmlMessage = '<p>Please register here:</p><p><a href="'.$url.'">'.$url.'</a></p>';

        $response = $this
            ->actingAs($user)
            ->post(route('profile.invitations.send', $invitation), [
                'recipient_email' => 'invitee@example.com',
                'subject' => 'Join EFGTrack',
                'message' => $htmlMessage,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        Mail::assertSent(InvitationLinkMail::class, fn (InvitationLinkMail $mail): bool => str_contains($mail->emailBody, $url));
    }

    public function test_member_cannot_mail_invitation_to_email_with_active_invitation(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $invitation = RegistrationInvitation::factory()->for($user, 'sponsor')->create([
            'email' => null,
        ]);
        RegistrationInvitation::factory()->for($user, 'sponsor')->create([
            'email' => 'invitee@example.com',
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('profile.invitations.send', $invitation), [
                'recipient_email' => 'invitee@example.com',
                'subject' => 'Join EFGTrack',
                'message' => "Please register here:\n".$invitation->invitationUrl(),
            ]);

        $response->assertSessionHasErrors('recipient_email');

        Mail::assertNothingSent();
    }

    public function test_invitation_email_message_must_include_registration_link(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $invitation = RegistrationInvitation::factory()->for($user, 'sponsor')->create();

        $response = $this
            ->actingAs($user)
            ->post(route('profile.invitations.send', $invitation), [
                'recipient_email' => 'invitee@example.com',
                'subject' => 'Join EFGTrack',
                'message' => 'Please register soon.',
            ]);

        $response->assertSessionHasErrors('message');

        Mail::assertNothingSent();
    }

    public function test_member_cannot_delete_their_own_account_from_profile(): void
    {
        $user = User::factory()->create();
        $user->profile()->create([
            'efg_associate_id' => 'EFG-DELETE-1',
        ]);
        $invitation = RegistrationInvitation::factory()->for($user, 'sponsor')->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response->assertMethodNotAllowed();

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh());
        $this->assertNull(User::withTrashed()->find($user->id)?->deleted_at);
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('registration_invitations', [
            'id' => $invitation->id,
            'deleted_at' => null,
        ]);
    }

    public function test_profile_delete_route_is_not_available_even_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response->assertMethodNotAllowed();

        $this->assertNotNull($user->fresh());
    }
}
