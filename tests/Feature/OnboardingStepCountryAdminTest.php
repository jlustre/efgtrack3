<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OnboardingStepCountryAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_country_specific_onboarding_step(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('admin.management.store', 'onboarding-steps'), [
                'title' => 'Canada: Confirm Provincial Exam Booking',
                'description' => 'Confirm provincial exam booking details.',
                'sort_order' => 300,
                'responsible_parties' => 'Self, CFM',
                'notified_parties' => 'SP, CFM',
                'is_active' => 1,
                'is_required' => 1,
                'country' => 'Canada',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('onboarding_steps', [
            'title' => 'Canada: Confirm Provincial Exam Booking',
            'country' => 'Canada',
            'notified_parties' => 'SP, CFM',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_global_onboarding_step(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('admin.management.store', 'onboarding-steps'), [
                'title' => 'Global: Confirm Portal Access',
                'description' => 'Confirm login and dashboard access.',
                'sort_order' => 305,
                'responsible_parties' => 'Self',
                'notified_parties' => 'SP',
                'is_active' => 1,
                'is_required' => 1,
                'country' => '',
            ])
            ->assertRedirect();

        $this->assertNull(DB::table('onboarding_steps')->where('title', 'Global: Confirm Portal Access')->value('country'));
    }
}
