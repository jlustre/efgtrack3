<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pre_employment_member_sees_my_dashboard_pre_employment_and_my_messages(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        Profile::query()->create([
            'user_id' => $user->id,
            'is_efg_active_associate' => true,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('My Dashboard', false)
            ->assertSee('Pre-employment', false)
            ->assertSee('My Messages', false)
            ->assertDontSee('My Employment', false);
    }

    public function test_employee_sees_my_employment_instead_of_pre_employment(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        Profile::query()->create([
            'user_id' => $user->id,
            'is_efg_active_associate' => true,
            'recruited_at' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('My Dashboard', false)
            ->assertSee('My Employment', false)
            ->assertSee('My Messages', false)
            ->assertDontSee('Pre-employment', false);
    }

    public function test_employment_link_highlights_on_employment_page(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        Profile::query()->create([
            'user_id' => $user->id,
            'recruited_at' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->get(route('employment.index'))
            ->assertOk()
            ->assertSee('data-server-active-item="top-my-employment"', false);
    }

    public function test_pre_employment_link_highlights_on_pre_employment_page(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        Profile::query()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('pre-employment.index'))
            ->assertOk()
            ->assertSee('data-server-active-item="top-pre-employment"', false);
    }

    public function test_messages_link_highlights_on_messages_page(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        Profile::query()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('messages.index'))
            ->assertOk()
            ->assertSee('data-server-active-item="top-my-messages"', false);
    }
}
