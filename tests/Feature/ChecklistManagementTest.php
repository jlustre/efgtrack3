<?php

namespace Tests\Feature;

use App\Models\Checklist;
use App\Models\ChecklistType;
use App\Models\User;
use Database\Seeders\ChecklistSeeder;
use Database\Seeders\ChecklistTypeSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChecklistManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_checklist_seeder_populates_all_types(): void
    {
        $this->seed(ChecklistTypeSeeder::class);
        $this->seed(ChecklistSeeder::class);

        $this->assertDatabaseHas('checklists', [
            'title' => 'Complete Member Profile',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('checklists', [
            'title' => 'Pass Licensing Exam',
            'is_active' => true,
        ]);

        $onboardingTypeId = ChecklistType::query()->where('code', 'onboarding')->value('id');
        $this->assertGreaterThan(0, Checklist::query()->where('checklist_type_id', $onboardingTypeId)->count());

        $mentoringTypeId = ChecklistType::query()->where('code', 'cfm-mentoring')->value('id');
        $this->assertGreaterThan(0, Checklist::query()->where('checklist_type_id', $mentoringTypeId)->count());
    }

    public function test_admin_can_filter_checklists_by_type(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $this->seed(ChecklistTypeSeeder::class);
        $this->seed(ChecklistSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $onboardingTypeId = ChecklistType::query()->where('code', 'onboarding')->value('id');
        $licensingTypeId = ChecklistType::query()->where('code', 'licensing')->value('id');

        $this->actingAs($admin)
            ->get(route('admin.management.resource.index', ['checklists', 'checklist_type' => $onboardingTypeId]))
            ->assertOk()
            ->assertSee('Complete Member Profile')
            ->assertDontSee('Pass Licensing Exam');

        $this->actingAs($admin)
            ->get(route('admin.management.resource.index', ['checklists', 'checklist_type' => $licensingTypeId]))
            ->assertOk()
            ->assertSee('Pass Licensing Exam')
            ->assertDontSee('Complete Member Profile');
    }

    public function test_admin_can_update_checklist_seeder_from_list(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $this->seed(ChecklistTypeSeeder::class);
        $this->seed(ChecklistSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $seederPath = database_path('seeders/ChecklistSeeder.php');
        $phasesPath = database_path('seeders/data/cfm_mentoring_phases.php');
        $originalSeeder = file_get_contents($seederPath);
        $originalPhases = file_get_contents($phasesPath);

        $licensingTypeId = ChecklistType::query()->where('code', 'licensing')->value('id');

        Checklist::query()->create([
            'checklist_type_id' => $licensingTypeId,
            'title' => 'Seed Export Test Step',
            'description' => 'Exported from admin checklist management.',
            'sort_order' => 9990,
            'is_required' => true,
            'responsible_parties' => 'Self, SP',
            'notified_parties' => 'SP',
            'is_active' => true,
        ]);

        try {
            $this->actingAs($admin)
                ->get(route('admin.management.resource.index', 'checklists'))
                ->assertOk()
                ->assertSee('Update Seeder');

            $this->actingAs($admin)
                ->post(route('admin.management.update-seeder', 'checklists'))
                ->assertRedirect(route('admin.management.resource.index', 'checklists'))
                ->assertSessionHas('status', 'seeder-updated');

            $seeder = file_get_contents($seederPath);

            $this->assertStringContainsString('Seed Export Test Step', $seeder);
            $this->assertStringContainsString('Exported from admin checklist management.', $seeder);
            $this->assertStringContainsString("upsertItem('licensing'", $seeder);
        } finally {
            file_put_contents($seederPath, $originalSeeder);
            file_put_contents($phasesPath, $originalPhases);
        }
    }

    public function test_admin_can_manage_checklists(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $this->seed(ChecklistTypeSeeder::class);
        $this->seed(ChecklistSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $typeId = ChecklistType::query()->where('code', 'licensing')->value('id');

        $this->actingAs($admin)
            ->get(route('admin.management.resource.index', 'checklists'))
            ->assertOk()
            ->assertSee('Checklists')
            ->assertSee('Complete Member Profile');

        $this->actingAs($admin)
            ->post(route('admin.management.store', 'checklists'), [
                'checklist_type_id' => $typeId,
                'title' => 'Custom Licensing Step',
                'description' => 'A custom licensing milestone.',
                'sort_order' => 999,
                'is_required' => 1,
                'responsible_parties' => 'Self, SP',
                'notified_parties' => 'SP',
                'country' => '',
                'group_label' => '',
                'phase_number' => '',
                'phase_title' => '',
                'phase_target' => '',
                'section_title' => '',
                'slug' => '',
                'action_url' => '',
                'action_label' => '',
                'is_active' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('checklists', [
            'checklist_type_id' => $typeId,
            'title' => 'Custom Licensing Step',
            'is_active' => true,
        ]);
    }
}
