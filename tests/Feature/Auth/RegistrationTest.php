<?php

namespace Tests\Feature\Auth;

use App\Models\Rank;
use App\Models\RegistrationInvitation;
use App\Models\Team;
use App\Models\User;
use App\Support\LocationOptions;
use Database\Seeders\CountrySeeder;
use Database\Seeders\EmailTemplateSeeder;
use Database\Seeders\ProfileCompletionFieldSeeder;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StateProvinceSeeder;
use Database\Seeders\TimezoneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            CountrySeeder::class,
            TimezoneSeeder::class,
        ]);
    }

    /**
     * @return array{country_id: int|null, timezone_id: int|null}
     */
    private function registrationLocationIds(): array
    {
        return [
            'country_id' => LocationOptions::resolveCountryId('Canada'),
            'timezone_id' => LocationOptions::resolveTimezoneId('Canada Pacific Time'),
        ];
    }

    public function test_open_registration_screen_is_blocked_without_invitation(): void
    {
        $response = $this->get('/register');

        $response->assertForbidden();
    }

    public function test_registration_screen_can_be_rendered_with_valid_invitation(): void
    {
        $invitation = RegistrationInvitation::factory()->create();

        $response = $this->get(route('register.invitation', $invitation->code));

        $response
            ->assertStatus(200)
            ->assertSee($invitation->code)
            ->assertSee($invitation->sponsor->name)
            ->assertSee('Important Before Registering')
            ->assertSee('You must already be registered with Experior Financial Group before completing EFGTrack registration.')
            ->assertSee('If you have not finished your Experior enrollment, ask your sponsor how to proceed.')
            ->assertSee('Your Experior sponsor must be the same person as your EFGTrack sponsor')
            ->assertSee('If this is not the person who invited you, stop here and ask the correct sponsor to send their invitation link.')
            ->assertDontSee('Verified Sponsor')
            ->assertSee('State / Province')
            ->assertSee('British Columbia', false)
            ->assertSee('California', false);
    }

    public function test_registration_screen_shows_unavailable_message_for_unknown_invitation(): void
    {
        $response = $this->get(route('register.invitation', 'UNKNOWNCODE'));

        $response->assertForbidden();
    }

    public function test_new_users_can_register_with_valid_invitation(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            EmailTemplateSeeder::class,
            CountrySeeder::class,
            StateProvinceSeeder::class,
            TimezoneSeeder::class,
            ProfileCompletionFieldSeeder::class,
        ]);

        $sponsor = User::factory()->create();
        $team = Team::create([
            'owner_id' => $sponsor->id,
            'leader_id' => $sponsor->id,
            'name' => 'West Coast Builders',
            'is_active' => true,
        ]);
        $sponsor->forceFill(['team_id' => $team->id])->save();

        $invitation = RegistrationInvitation::factory()->for($sponsor, 'sponsor')->create([
            'code' => 'ABC123EFG',
        ]);

        $response = $this->post('/register', [
            'registration_code' => $invitation->code,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'efg_associate_id' => 'EFG-1001',
            'city' => 'Vancouver',
            'province' => 'British Columbia',
            'country' => 'Canada',
            'timezone' => 'Canada Pacific Time',
            'sponsor_confirmed' => '1',
            'active_associate_confirmed' => '1',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();

        $newUser = User::where('email', 'test@example.com')->firstOrFail();

        $this->assertSame($sponsor->id, $newUser->sponsor_id);
        $this->assertSame($team->id, $newUser->team_id);
        $this->assertSame(Rank::where('code', 'FA')->firstOrFail()->id, $newUser->rank_id);
        $this->assertSame('Test User', $newUser->name);
        $this->assertTrue($newUser->hasRole('member'));
        $this->assertNotNull($newUser->joined_at);
        $this->assertNotNull($newUser->last_login_at);
        $this->assertSame('127.0.0.1', $newUser->last_login_ip);
        $this->assertTrue($newUser->is_online);
        $this->assertSame('EFG-1001', $newUser->profile->efg_associate_id);
        $this->assertSame('Vancouver', $newUser->profile->city);
        $newUser->load('profile.countryRecord', 'profile.stateProvince', 'profile.timezoneRecord');
        $this->assertSame('British Columbia', $newUser->profile->province);
        $this->assertSame('Canada', $newUser->profile->country);
        $this->assertSame('Canada Pacific Time', $newUser->profile->timezone);
        $this->assertTrue($newUser->profile->is_efg_active_associate);
        $this->assertDatabaseHas('registration_invitations', [
            'id' => $invitation->id,
            'accepted_by' => $newUser->id,
            'uses_count' => 1,
        ]);
        $this->assertNotNull($invitation->refresh()->revoked_at);

        $response
            ->assertRedirect(route('dashboard', absolute: false))
            ->assertSessionHas('show_profile_completion_modal', true);

        $this->actingAs($newUser)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('forceProfileCompletionModal', true)
            ->assertSee('Complete your profile', false)
            ->assertSee('Profile completion', false)
            ->assertSee('Required fields', false);
    }

    public function test_registration_requires_sponsor_and_active_associate_confirmation(): void
    {
        $invitation = RegistrationInvitation::factory()->create();

        $response = $this->post('/register', [
            'registration_code' => $invitation->code,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'efg_associate_id' => 'EFG-1002',
            'city' => 'Vancouver',
            'province' => 'British Columbia',
            'country' => 'Canada',
            'timezone' => 'Canada Pacific Time',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'sponsor_confirmed',
            'active_associate_confirmed',
        ]);

        $this->assertGuest();
    }

    public function test_invitation_code_cannot_be_reused_after_acceptance(): void
    {
        $invitation = RegistrationInvitation::factory()->create([
            'uses_count' => 1,
            'max_uses' => 1,
        ]);

        $response = $this->post('/register', [
            'registration_code' => $invitation->code,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'efg_associate_id' => 'EFG-1003',
            'city' => 'Vancouver',
            'province' => 'British Columbia',
            'country' => 'Canada',
            'timezone' => 'Canada Pacific Time',
            'sponsor_confirmed' => '1',
            'active_associate_confirmed' => '1',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasErrors('registration_code');

        $this->assertGuest();
    }
}
