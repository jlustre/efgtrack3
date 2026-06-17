<?php

namespace Tests\Feature\Goals;

use App\Models\Goal;
use App\Models\GoalCategory;
use App\Models\User;
use App\Services\Goals\GoalService;
use App\Services\Goals\SmartGoalValidator;
use Database\Seeders\GoalBadgeSeeder;
use Database\Seeders\GoalCategorySeeder;
use Database\Seeders\GoalTemplateSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GoalsModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolePermissionSeeder::class,
            GoalCategorySeeder::class,
            GoalTemplateSeeder::class,
            GoalBadgeSeeder::class,
        ]);
    }

    public function test_member_can_view_goals_hub(): void
    {
        $user = User::factory()->create();
        $user->assignRole('member');

        $this->actingAs($user)
            ->get(route('goals.index'))
            ->assertOk()
            ->assertSee('Goals & Performance', false)
            ->assertSee('Total Goals', false);
    }

    public function test_smart_validator_scores_complete_goal_data(): void
    {
        $categoryId = GoalCategory::query()->where('slug', 'recruiting')->value('id');

        $result = app(SmartGoalValidator::class)->evaluate([
            'name' => 'Recruit four associates this month',
            'description' => 'Build team depth through presentations and registrations.',
            'target_value' => 4,
            'measurement_type' => 'number',
            'deadline_at' => now()->addMonth()->toDateString(),
            'starts_at' => now()->toDateString(),
            'metric_key' => 'recruits',
            'goal_category_id' => $categoryId,
        ]);

        $this->assertGreaterThanOrEqual(80, $result['score']);
    }

    public function test_goal_service_creates_goal_with_milestones(): void
    {
        $user = User::factory()->create();
        $user->assignRole('member');
        $categoryId = GoalCategory::query()->where('slug', 'prospecting')->value('id');

        $goal = app(GoalService::class)->create($user, [
            'goal_category_id' => $categoryId,
            'name' => 'Weekly prospecting push',
            'description' => 'Increase contacts and appointments every week.',
            'hierarchy_level' => 'weekly',
            'measurement_type' => 'number',
            'metric_key' => 'contacts',
            'target_value' => 25,
            'starts_at' => now()->toDateString(),
            'deadline_at' => now()->addWeek()->toDateString(),
        ], [
            ['name' => '50 calls', 'due_at' => now()->addDays(3)->toDateString(), 'target_value' => 50],
        ]);

        $this->assertDatabaseHas('goals', [
            'id' => $goal->id,
            'user_id' => $user->id,
            'name' => 'Weekly prospecting push',
        ]);
        $this->assertCount(1, $goal->milestones);
        $this->assertGreaterThan(0, $goal->smart_score);
    }

    public function test_goal_wizard_advances_after_category_selection(): void
    {
        $user = User::factory()->create();
        $user->assignRole('member');
        $categoryId = GoalCategory::query()->where('slug', 'recruiting')->value('id');

        Livewire::actingAs($user)
            ->test(\App\Livewire\Goals\GoalWizard::class)
            ->call('selectCategory', $categoryId)
            ->assertSet('goalCategoryId', $categoryId)
            ->call('nextStep')
            ->assertSet('step', 2);
    }

    public function test_goal_wizard_step_one_requires_category(): void
    {
        $user = User::factory()->create();
        $user->assignRole('member');

        Livewire::actingAs($user)
            ->test(\App\Livewire\Goals\GoalWizard::class)
            ->call('nextStep')
            ->assertHasErrors(['goalCategoryId']);
    }
}
