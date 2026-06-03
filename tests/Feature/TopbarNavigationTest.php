<?php

namespace Tests\Feature;

use App\Http\Controllers\TaskController;
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
use Illuminate\Support\Str;
use Tests\TestCase;

class TopbarNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_topbar_has_search_notifications_and_profile_menu_options(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Search members, training, resources...')
            ->assertSee('Open my tasks')
            ->assertSee('Open notifications')
            ->assertSee('Notifications')
            ->assertSee('My Profile')
            ->assertSee('Admin Management')
            ->assertSee('Log Out');
    }

    public function test_topbar_my_tasks_icon_shows_real_open_task_count(): void
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

        $user = User::where('email', 'agency-owner@efgtrack.com')->firstOrFail();
        $expectedCount = app(TaskController::class)->openTaskCountFor($user);

        $this->assertGreaterThan(0, $expectedCount);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Open my tasks')
            ->assertSee((string) $expectedCount);
    }

    public function test_topbar_notifications_icon_shows_real_unread_count_and_recent_alerts(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'type' => 'database',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode([
                'title' => 'Licensing item approved',
                'message' => 'Your licensing milestone was approved.',
                'category' => 'Licensing',
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Open notifications')
            ->assertSee('Licensing item approved')
            ->assertSee('1 unread update');
    }

    public function test_notification_center_can_mark_notifications_read(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');
        $notificationId = (string) Str::uuid();

        DB::table('notifications')->insert([
            'id' => $notificationId,
            'type' => 'database',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode([
                'title' => 'New training assigned',
                'message' => 'A training module is ready.',
                'category' => 'Training',
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('New training assigned')
            ->assertSee('Unread');

        $this->actingAs($user)
            ->post(route('notifications.mark-read', $notificationId))
            ->assertRedirect();

        $this->assertNotNull(DB::table('notifications')->where('id', $notificationId)->value('read_at'));
    }

    public function test_search_scaffold_accepts_query(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        $this->actingAs($user)
            ->get(route('search.index', ['q' => 'licensing']))
            ->assertOk()
            ->assertSee('Search Results')
            ->assertSee('licensing');
    }

    public function test_member_sidebar_destination_pages_are_accessible(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        $routes = [
            'dashboard',
            'onboarding.index',
            'licensing.index',
            'tasks.index',
            'apprenticeship.index',
            'cfm-training.index',
            'training.index',
            'assessments.index',
            'team.directs',
            'team.trainees',
            'team.downlines',
            'team.prospects',
            'announcements.index',
            'events.index',
            'calendar.index',
            'notifications.index',
            'rank-advancement.index',
            'recognition.index',
            'resources.documents',
            'resources.videos',
            'resources.recorded-webinars',
            'resources.zoom-links',
        ];

        foreach ($routes as $route) {
            $this->actingAs($user)
                ->get(route($route))
                ->assertOk();
        }
    }
}
