<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\DashboardStatsService;
use App\Services\ProfileCompletionService;
use App\Support\LocationOptions;
use Database\Seeders\CountrySeeder;
use Database\Seeders\FieldApprenticeshipProgramSeeder;
use Database\Seeders\LicensingStepSeeder;
use Database\Seeders\OnboardingStepSeeder;
use Database\Seeders\ProfileCompletionFieldSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StateProvinceSeeder;
use Database\Seeders\TimezoneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_stat_cards_show_zero_tracker_progress_for_new_user(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            ProfileCompletionFieldSeeder::class,
        ]);

        $user = User::factory()->create();
        $user->assignRole('member');

        $profileCompletion = app(ProfileCompletionService::class)->snapshot($user);
        $statCards = app(DashboardStatsService::class)->statCards($user, $profileCompletion);

        $this->assertSame('0%', $statCards[1]['value']);
        $this->assertSame('0%', $statCards[2]['value']);
        $this->assertSame('0%', $statCards[3]['value']);
        $this->assertSame('0%', $statCards[4]['value']);

        $this->assertSame('My Profile', $statCards[0]['label']);
        $this->assertSame('My Onboarding', $statCards[1]['label']);
        $this->assertSame('My Credentials', $statCards[2]['label']);
        $this->assertSame('My Apprenticeship', $statCards[3]['label']);
        $this->assertSame('My Trainings', $statCards[4]['label']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('statCards', $statCards)
            ->assertSee('My Profile', false)
            ->assertSee('My Onboarding', false)
            ->assertSee('My Credentials', false)
            ->assertSee('My Apprenticeship', false)
            ->assertSee('My Trainings', false)
            ->assertDontSee('65%', false)
            ->assertDontSee('40%', false)
            ->assertDontSee('25%', false)
            ->assertDontSee('70%', false);
    }

    public function test_dashboard_stat_cards_reflect_real_tracker_progress(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
            StateProvinceSeeder::class,
            TimezoneSeeder::class,
            ProfileCompletionFieldSeeder::class,
            OnboardingStepSeeder::class,
            LicensingStepSeeder::class,
            FieldApprenticeshipProgramSeeder::class,
        ]);

        $user = User::factory()->create();
        $user->assignRole('member');
        $user->profile()->create(array_merge([
            'is_efg_active_associate' => true,
            'efg_associate_id' => 'EFG-DASH-1',
        ], LocationOptions::profileLocationIds('Canada')));

        $onboardingStepId = DB::table('onboarding_steps')
            ->where('title', 'Complete Member Profile')
            ->value('id');

        DB::table('user_onboarding_progress')->insert([
            'user_id' => $user->id,
            'onboarding_step_id' => $onboardingStepId,
            'status' => 'completed',
            'completed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $licensingStepId = DB::table('licensing_steps')
            ->where('title', 'Confirm Licensing Jurisdiction')
            ->value('id');

        DB::table('user_licensing_progress')->insert([
            'user_id' => $user->id,
            'licensing_step_id' => $licensingStepId,
            'status' => 'completed',
            'completed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $apprenticeshipStepId = DB::table('apprenticeship_steps')
            ->where('title', 'FAP Orientation With Sponsor And CFM')
            ->value('id');

        DB::table('user_apprenticeship_progress')->insert([
            'user_id' => $user->id,
            'apprenticeship_step_id' => $apprenticeshipStepId,
            'status' => 'completed',
            'completed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dashboardStats = app(DashboardStatsService::class);
        $profileCompletion = app(ProfileCompletionService::class)->snapshot($user);
        $expected = $dashboardStats->statCards($user, $profileCompletion);

        $this->assertGreaterThan(0, $expected[0]['bar']);
        $this->assertGreaterThan(0, $expected[1]['bar']);
        $this->assertGreaterThan(0, $expected[2]['bar']);
        $this->assertGreaterThan(0, $expected[3]['bar']);
        $this->assertSame(0, $expected[4]['bar']);

        $this->assertSame('My Profile', $expected[0]['label']);
        $this->assertSame('My Onboarding', $expected[1]['label']);
        $this->assertSame('My Credentials', $expected[2]['label']);
        $this->assertSame('My Apprenticeship', $expected[3]['label']);
        $this->assertSame('My Trainings', $expected[4]['label']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('statCards', $expected)
            ->assertSee('My Profile', false)
            ->assertSee('My Credentials', false)
            ->assertSee('My Trainings', false)
            ->assertSee($expected[0]['value'], false)
            ->assertSee($expected[1]['value'], false)
            ->assertSee($expected[2]['value'], false)
            ->assertSee($expected[3]['value'], false)
            ->assertSee('0%', false);
    }

    public function test_dashboard_handles_user_without_profile(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            ProfileCompletionFieldSeeder::class,
            OnboardingStepSeeder::class,
        ]);

        $user = User::factory()->create();
        $user->assignRole('member');

        $profileCompletion = app(ProfileCompletionService::class)->snapshot($user);
        $statCards = app(DashboardStatsService::class)->statCards($user, $profileCompletion);

        $this->assertIsArray($statCards);
        $this->assertCount(5, $statCards);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('statCards');
    }
}
