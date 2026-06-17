<?php

namespace Tests\Feature;

use App\Models\ChecklistType;
use App\Models\User;
use Database\Seeders\ChecklistTypeSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChecklistTypeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_checklist_type_seeder_populates_default_types(): void
    {
        $this->seed(ChecklistTypeSeeder::class);

        $this->assertDatabaseHas('checklist_types', [
            'code' => 'onboarding',
            'name' => 'Onboarding',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('checklist_types', [
            'code' => 'cfm-mentoring',
            'name' => 'CFM Mentoring',
        ]);

        $this->assertSame(7, ChecklistType::query()->count());
    }

    public function test_admin_can_manage_checklist_types(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $this->seed(ChecklistTypeSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.management.resource.index', 'checklist-types'))
            ->assertOk()
            ->assertSee('Checklist Types')
            ->assertSee('onboarding')
            ->assertSee('CFM Mentoring');

        $this->actingAs($admin)
            ->post(route('admin.management.store', 'checklist-types'), [
                'code' => 'custom-type',
                'name' => 'Custom Checklist',
                'description' => 'A custom checklist category.',
                'icon' => 'star',
                'sort_order' => 99,
                'is_active' => 1,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('checklist_types', [
            'code' => 'custom-type',
            'name' => 'Custom Checklist',
            'sort_order' => 99,
        ]);
    }

    public function test_checklists_hub_links_to_checklist_types(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.checklists.index'))
            ->assertOk()
            ->assertSee('Checklist Types')
            ->assertSee(route('admin.management.resource.index', 'checklist-types'), false);
    }
}
