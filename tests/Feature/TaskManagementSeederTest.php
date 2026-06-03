<?php

namespace Tests\Feature;

use App\Models\TaskSuggestion;
use App\Models\User;
use App\Models\UserTask;
use Database\Seeders\CfmTrainingModuleSeeder;
use Database\Seeders\FieldApprenticeshipProgramSeeder;
use Database\Seeders\LicensingStepSeeder;
use Database\Seeders\OnboardingStepSeeder;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TaskManagementSeeder;
use Database\Seeders\TaskScenarioSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_management_seeder_populates_database_backed_tasks(): void
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
            TaskManagementSeeder::class,
        ]);

        $agencyOwner = User::where('email', 'agency-owner@efgtrack.com')->firstOrFail();

        $this->assertGreaterThanOrEqual(6, UserTask::count());
        $this->assertGreaterThanOrEqual(5, TaskSuggestion::count());
        $this->assertDatabaseHas('user_tasks', [
            'title' => 'Follow up with David Kim on life insurance application',
            'assigned_to_user_id' => $agencyOwner->id,
            'status' => 'overdue',
        ]);

        $davidTask = UserTask::where('title', 'Follow up with David Kim on life insurance application')->firstOrFail();
        $this->assertGreaterThanOrEqual(2, $davidTask->checklistItems()->count());
        $this->assertGreaterThanOrEqual(1, $davidTask->comments()->count());

        $this->actingAs($agencyOwner)
            ->get(route('tasks.index'))
            ->assertOk()
            ->assertSee('Follow up with David Kim on life insurance application', false)
            ->assertSee('AI-Powered Task Suggestions', false)
            ->assertSee('Follow up with hot prospects approaching close window', false)
            ->assertSee('Left a voicemail', false);
    }
}
