<?php

namespace Tests\Feature;

use App\Events\NewMemberRegistered;
use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
use App\Models\Notification;
use App\Models\RegistrationInvitation;
use App\Models\Team;
use App\Models\User;
use App\Services\NewMemberRegistrationService;
use Database\Seeders\CountrySeeder;
use Database\Seeders\EmailTemplateSeeder;
use Database\Seeders\NotificationConfigSeeder;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StateProvinceSeeder;
use Database\Seeders\TimezoneSeeder;
use App\Support\LocationOptions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NewMemberRegistrationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            CountrySeeder::class,
            StateProvinceSeeder::class,
            TimezoneSeeder::class,
            RankSeeder::class,
            RolePermissionSeeder::class,
            EmailTemplateSeeder::class,
            NotificationConfigSeeder::class,
        ]);
    }

    public function test_active_member_can_generate_invitation_link_from_profile(): void
    {
        $member = User::factory()->create(['is_active' => true]);
        $member->assignRole('member');

        $response = $this
            ->actingAs($member)
            ->post(route('profile.invitations.store'), [
                'email' => 'prospect@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('profile.edit', ['open_invitations' => 1]));

        $invitation = RegistrationInvitation::query()
            ->where('sponsor_id', $member->id)
            ->where('email', 'prospect@example.com')
            ->first();

        $this->assertNotNull($invitation);
        $this->assertSame(route('register.invitation', $invitation->code), session('invitation_url'));
    }

    public function test_inactive_member_cannot_generate_invitation_link(): void
    {
        $member = User::factory()->create(['is_active' => false]);
        $member->assignRole('member');

        $this
            ->actingAs($member)
            ->post(route('profile.invitations.store'))
            ->assertRedirect(route('login'));
    }

    public function test_registration_page_prefills_code_and_sponsor(): void
    {
        $sponsor = User::factory()->create(['name' => 'Invite Sponsor']);
        $invitation = RegistrationInvitation::factory()->for($sponsor, 'sponsor')->create([
            'code' => 'PREFILL12345',
        ]);

        $this
            ->get(route('register.invitation', $invitation->code))
            ->assertOk()
            ->assertSee('PREFILL12345', false)
            ->assertSee('Invite Sponsor', false)
            ->assertSee('value="PREFILL12345"', false);
    }

    public function test_registration_dispatches_new_member_registered_event(): void
    {
        Event::fake([NewMemberRegistered::class]);

        [$invitation] = $this->createInvitationContext();

        $this->post('/register', $this->registrationPayload($invitation));

        Event::assertDispatched(NewMemberRegistered::class, function (NewMemberRegistered $event): bool {
            return $event->member->email === 'new.recruit@example.com'
                && $event::TRIGGER === 'new_member_registration';
        });
    }

    public function test_registration_sends_welcome_emails_to_member_sponsor_and_agency_owner(): void
    {
        Mail::fake();

        [$invitation, $sponsor, $agencyOwner] = $this->createInvitationContext(withAgencyOwner: true);

        $this->post('/register', $this->registrationPayload($invitation));

        Mail::assertSent(TemplatedMail::class, 4);
        Mail::assertSent(TemplatedMail::class, fn (TemplatedMail $mail): bool => $mail->hasTo('new.recruit@example.com')
            && str_contains($mail->customSubject, 'Welcome to')
            && str_contains($mail->emailBody, 'New Recruit'));
        Mail::assertSent(TemplatedMail::class, fn (TemplatedMail $mail): bool => $mail->hasTo('new.recruit@example.com')
            && str_contains($mail->customSubject, 'Verify your email')
            && str_contains($mail->emailBody, 'verify-email'));
        Mail::assertSent(TemplatedMail::class, fn (TemplatedMail $mail): bool => $mail->hasTo($sponsor->email)
            && str_contains($mail->customSubject, 'joined')
            && str_contains($mail->emailBody, $sponsor->name));
        Mail::assertSent(TemplatedMail::class, fn (TemplatedMail $mail): bool => $mail->hasTo($agencyOwner->email)
            && str_contains($mail->customSubject, 'New Associate'));
    }

    public function test_registration_creates_cfm_assignment_notifications_for_sponsor_and_agency_owner(): void
    {
        [$invitation, $sponsor, $agencyOwner] = $this->createInvitationContext(withAgencyOwner: true);

        $this->post('/register', $this->registrationPayload($invitation));

        $this->assertTrue(
            Notification::query()
                ->where('notifiable_id', $agencyOwner->id)
                ->where('data->trigger', 'assign_cfm_reminder')
                ->where('data->category', 'Mentor Assignment')
                ->exists()
        );

        $this->assertTrue(
            Notification::query()
                ->where('notifiable_id', $sponsor->id)
                ->where('data->trigger', 'recommend_cfm_reminder')
                ->where('data->category', 'Mentor Assignment')
                ->exists()
        );
    }

    public function test_registration_persists_notifications_for_dashboard_and_topbar(): void
    {
        [$invitation, $sponsor, $agencyOwner] = $this->createInvitationContext(withAgencyOwner: true);

        $this->post('/register', $this->registrationPayload($invitation));

        $this->assertSame(1, $sponsor->fresh()->unreadNotifications()->count());
        $this->assertSame(1, $agencyOwner->fresh()->unreadNotifications()->count());

        $this->actingAs($sponsor)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Recommend a CFM for New Recruit', false)
            ->assertSee('remind '.$agencyOwner->name.' to assign one', false)
            ->assertSee('1 unread update', false)
            ->assertSee('Mentor Assignment', false);

        $this->actingAs($agencyOwner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Assign a CFM for New Recruit', false)
            ->assertSee('1 unread update', false);

        $this->actingAs($sponsor)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('1 unread update', false);
    }

    public function test_welcome_email_templates_are_required_for_registration_emails(): void
    {
        EmailTemplate::query()->where('key', 'new_member_welcome')->delete();

        [, $sponsor] = $this->createInvitationContext();
        $member = User::factory()->unverified()->create([
            'email' => 'new.recruit@example.com',
            'sponsor_id' => $sponsor->id,
            'team_id' => $sponsor->team_id,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('new_member_welcome');

        app(NewMemberRegistrationService::class)->process($member);
    }

    public function test_email_verification_template_is_required_for_registration(): void
    {
        EmailTemplate::query()->where('key', 'new_member_email_verification')->delete();

        [, $sponsor] = $this->createInvitationContext();
        $member = User::factory()->unverified()->create([
            'email' => 'verify-required@example.com',
            'sponsor_id' => $sponsor->id,
            'team_id' => $sponsor->team_id,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('new_member_email_verification');

        $member->sendEmailVerificationNotification();
    }

    public function test_registration_service_skips_verification_email_for_verified_member(): void
    {
        Mail::fake();

        [, $sponsor] = $this->createInvitationContext(withAgencyOwner: true);
        $member = User::factory()->create([
            'email' => 'verified@example.com',
            'sponsor_id' => $sponsor->id,
            'team_id' => $sponsor->team_id,
            'email_verified_at' => now(),
        ]);

        app(NewMemberRegistrationService::class)->process($member);

        Mail::assertSent(TemplatedMail::class, 3);
        Mail::assertNotSent(TemplatedMail::class, fn (TemplatedMail $mail): bool => str_contains($mail->customSubject, 'Verify your email'));

        $member->sendEmailVerificationNotification();

        Mail::assertSent(TemplatedMail::class, 3);
    }

    public function test_sponsor_who_is_agency_owner_receives_single_notification(): void
    {
        $agencyOwner = User::factory()->create(['name' => 'Agency Owner Sponsor']);
        $agencyOwner->assignRole('agency-owner');

        $team = Team::create([
            'owner_id' => $agencyOwner->id,
            'leader_id' => $agencyOwner->id,
            'name' => 'Owner Team',
            'is_active' => true,
        ]);
        $agencyOwner->forceFill(['team_id' => $team->id])->save();

        $invitation = RegistrationInvitation::factory()->for($agencyOwner, 'sponsor')->create([
            'code' => 'OWNERINVITE1',
        ]);

        $this->post('/register', $this->registrationPayload($invitation, email: 'owner.recruit@example.com', associateId: 'EFG-OWNER-1'));

        $this->assertSame(
            1,
            Notification::query()
                ->where('notifiable_id', $agencyOwner->id)
                ->where('data->trigger', 'assign_cfm_reminder')
                ->count()
        );

        $this->assertFalse(
            Notification::query()
                ->where('notifiable_id', $agencyOwner->id)
                ->where('data->trigger', 'recommend_cfm_reminder')
                ->exists()
        );
    }

    public function test_sponsor_who_is_agency_owner_receives_agency_owner_welcome_email(): void
    {
        Mail::fake();

        $agencyOwner = User::factory()->create([
            'name' => 'Agency Owner Sponsor',
            'email' => 'ao-sponsor@example.com',
        ]);
        $agencyOwner->assignRole('agency-owner');

        $team = Team::create([
            'owner_id' => $agencyOwner->id,
            'leader_id' => $agencyOwner->id,
            'name' => 'Owner Team',
            'is_active' => true,
        ]);
        $agencyOwner->forceFill(['team_id' => $team->id])->save();

        $invitation = RegistrationInvitation::factory()->for($agencyOwner, 'sponsor')->create([
            'code' => 'OWNERINVITE2',
        ]);

        $this->post('/register', $this->registrationPayload($invitation, email: 'owner.recruit2@example.com', associateId: 'EFG-OWNER-2'));

        Mail::assertSent(TemplatedMail::class, 4);
        Mail::assertSent(TemplatedMail::class, fn (TemplatedMail $mail): bool => $mail->hasTo('owner.recruit2@example.com'));
        Mail::assertSent(TemplatedMail::class, fn (TemplatedMail $mail): bool => $mail->hasTo('owner.recruit2@example.com')
            && str_contains($mail->customSubject, 'Verify your email'));
        Mail::assertSent(TemplatedMail::class, fn (TemplatedMail $mail): bool => $mail->hasTo('ao-sponsor@example.com')
            && str_contains($mail->customSubject, 'joined'));
        Mail::assertSent(TemplatedMail::class, fn (TemplatedMail $mail): bool => $mail->hasTo('ao-sponsor@example.com')
            && str_contains($mail->customSubject, 'New Associate'));
    }

    /**
     * @return array{0: RegistrationInvitation, 1: User, 2: User}
     */
    private function createInvitationContext(bool $withAgencyOwner = false): array
    {
        $agencyOwner = User::factory()->create(['name' => 'Agency Owner']);
        $agencyOwner->assignRole('agency-owner');

        $team = Team::create([
            'owner_id' => $agencyOwner->id,
            'leader_id' => $agencyOwner->id,
            'name' => 'Growth Team',
            'is_active' => true,
        ]);
        $agencyOwner->forceFill(['team_id' => $team->id])->save();

        $sponsor = User::factory()->create([
            'name' => 'Direct Sponsor',
            'sponsor_id' => $withAgencyOwner ? $agencyOwner->id : null,
            'team_id' => $team->id,
        ]);
        $sponsor->assignRole('member');

        $invitation = RegistrationInvitation::factory()->for($sponsor, 'sponsor')->create([
            'code' => 'WORKFLOW1234',
        ]);

        return [$invitation, $sponsor, $agencyOwner];
    }

    private function registrationPayload(
        RegistrationInvitation $invitation,
        string $email = 'new.recruit@example.com',
        string $associateId = 'EFG-9001',
    ): array {
        $countryId = LocationOptions::resolveCountryId('Canada');
        $stateProvinceId = LocationOptions::resolveStateProvinceId('Canada', 'Ontario');
        $timezoneId = LocationOptions::resolveTimezoneId('Canada Eastern Time');

        return [
            'registration_code' => $invitation->code,
            'first_name' => 'New',
            'last_name' => 'Recruit',
            'email' => $email,
            'efg_associate_id' => $associateId,
            'city' => 'Toronto',
            'country_id' => $countryId,
            'state_province_id' => $stateProvinceId,
            'timezone_id' => $timezoneId,
            'sponsor_confirmed' => '1',
            'active_associate_confirmed' => '1',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
    }
}
