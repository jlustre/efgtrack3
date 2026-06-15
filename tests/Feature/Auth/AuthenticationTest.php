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

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $this->assertFalse($user->refresh()->is_online);
        $response->assertRedirect('/');
    }
}
