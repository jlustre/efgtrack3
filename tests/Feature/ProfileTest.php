<?php

namespace Tests\Feature;

use App\Models\RegistrationInvitation;
use App\Models\User;
use App\Mail\InvitationLinkMail;
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
            ->assertDontSee('Delete Account');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '555-123-4567',
                'province' => 'Ontario',
                'city' => 'Toronto',
                'license_number' => 'LIC-123',
                'efg_associate_id' => 'EFG-2001',
                'bio' => 'Building a strong financial services team.',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
        $this->assertSame('555-123-4567', $user->profile->phone);
        $this->assertSame('Ontario', $user->profile->province);
        $this->assertSame('Toronto', $user->profile->city);
        $this->assertSame('LIC-123', $user->profile->license_number);
        $this->assertSame('EFG-2001', $user->profile->efg_associate_id);
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
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_member_can_create_invitation_link_from_profile(): void
    {
        $user = User::factory()->create();

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
        $user = User::factory()->create();

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
        $user = User::factory()->create();
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
        $user = User::factory()->create();
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
