<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CfmTrainingModuleSeeder;
use Database\Seeders\FieldApprenticeshipProgramSeeder;
use Database\Seeders\LicensingStepSeeder;
use Database\Seeders\OnboardingStepSeeder;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TaskScenarioSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TaskScenarioSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_scenario_seeder_creates_demo_task_records(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            TeamSeeder::class,
            OnboardingStepSeeder::class,
            LicensingStepSeeder::class,
            FieldApprenticeshipProgramSeeder::class,
            CfmTrainingModuleSeeder::class,
            TaskScenarioSeeder::class,
        ]);

        $agencyOwner = User::where('email', 'agency-owner@efgtrack.com')->firstOrFail();

        $this->assertDatabaseHas('users', [
            'email' => 'sofia.needsmentor@example.com',
            'sponsor_id' => $agencyOwner->id,
            'mentor_id' => null,
        ]);

        $this->assertSame(1, DB::table('user_onboarding_progress')->where('status', 'pending_confirmation')->count());
        $this->assertSame(1, DB::table('user_licensing_progress')->where('status', 'pending_confirmation')->count());
        $this->assertSame(1, DB::table('user_apprenticeship_progress')->where('status', 'pending_confirmation')->count());
        $this->assertSame(1, DB::table('cfm_training_progress')->where('status', 'pending_confirmation')->count());
        $this->assertSame(2, DB::table('registration_invitations')->where('sponsor_id', $agencyOwner->id)->whereNull('last_emailed_at')->count());
        $this->assertSame(1, DB::table('user_rank_progress')->where('status', 'ready_for_review')->count());

        $this->actingAs($agencyOwner)
            ->get(route('tasks.index'))
            ->assertOk()
            ->assertSee('Pass Licensing Exam')
            ->assertSee('Receive FAP Approval')
            ->assertSee('CFM Certification Review')
            ->assertSee('Assign a CFM to Sofia Reyes')
            ->assertSee('prospect.one@example.com')
            ->assertSee('Rank advancement')
            ->assertSee('Manage users');
    }
}
