<?php

namespace Tests\Feature\Auth;

use App\Models\Profile;
use App\Models\User;
use App\Support\LocationOptions;
use Database\Seeders\CountrySeeder;
use Database\Seeders\ProfileCompletionFieldSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StateProvinceSeeder;
use Database\Seeders\TimezoneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $user->refresh();

        $this->assertNotNull($user->last_login_at);
        $this->assertSame('127.0.0.1', $user->last_login_ip);
        $this->assertTrue($user->is_online);

        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_login_prompts_profile_completion_modal_when_profile_is_incomplete(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
            StateProvinceSeeder::class,
            TimezoneSeeder::class,
            ProfileCompletionFieldSeeder::class,
        ]);

        $user = User::factory()->create([
            'name' => 'Incomplete Member',
            'email' => 'incomplete@example.com',
        ]);
        $user->assignRole('member');

        Profile::query()->create([
            'user_id' => $user->id,
            'city' => 'Vancouver',
            'country_id' => LocationOptions::resolveCountryId('Canada'),
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('dashboard', absolute: false))
            ->assertSessionHas('show_profile_completion_modal', true);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('forceProfileCompletionModal', true)
            ->assertSee('Complete your profile', false)
            ->assertSee('My Profile', false);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('forceProfileCompletionModal', false);
    }

    public function test_profile_completion_modal_persists_province_and_efg_fields(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
            StateProvinceSeeder::class,
            TimezoneSeeder::class,
            ProfileCompletionFieldSeeder::class,
        ]);

        $user = User::factory()->create([
            'name' => 'Modal Member',
            'email' => 'modal@example.com',
        ]);
        $user->assignRole('member');

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'redirect_to' => 'dashboard',
                'name' => 'Modal Member',
                'email' => 'modal@example.com',
                'city' => 'Toronto',
                'country_id' => LocationOptions::resolveCountryId('Canada'),
                'state_province_id' => LocationOptions::resolveStateProvinceId('Canada', 'Ontario'),
                'timezone_id' => LocationOptions::resolveTimezoneId('Canada Eastern Time'),
                'efg_associate_id' => 'EFG-MODAL-1',
                'efg_invite_link' => 'https://experiorfinancial.com/invite/modal-1',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $user->refresh()->load('profile.stateProvince');

        $this->assertSame('Ontario', $user->profile->province);
        $this->assertSame('EFG-MODAL-1', $user->profile->efg_associate_id);
        $this->assertSame('https://experiorfinancial.com/invite/modal-1', $user->profile->efg_invite_link);

        $ontarioId = LocationOptions::resolveStateProvinceId('Canada', 'Ontario');

        $this->actingAs($user)
            ->get(route('profile.edit', ['tab' => 'profile']))
            ->assertOk()
            ->assertSee('value="'.$ontarioId.'" selected', false);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $this->assertFalse($user->refresh()->is_online);
        $response->assertRedirect('/');
    }
}
