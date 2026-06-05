<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_and_admin_can_open_admin_management(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($superAdmin)
            ->get(route('admin.management.index'))
            ->assertOk()
            ->assertSee('Operations Table Management')
            ->assertSee('Search table name, description, or key')
            ->assertSee('All categories')
            ->assertSee('Manage');

        $this->actingAs($admin)
            ->get(route('admin.management.index'))
            ->assertOk()
            ->assertSee('Operations Table Management')
            ->assertSee('Ranks')
            ->assertSee('Teams');
    }

    public function test_admin_management_index_supports_search_filter_and_pagination(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.management.index', ['search' => 'rank']))
            ->assertOk()
            ->assertSee('>ranks</div>', false)
            ->assertSee('>rank_requirements</div>', false)
            ->assertDontSee('>booking_links</div>', false);

        $this->actingAs($admin)
            ->get(route('admin.management.index', ['category' => 'booking']))
            ->assertOk()
            ->assertSee('>booking_links</div>', false)
            ->assertSee('>bookings</div>', false)
            ->assertDontSee('>ranks</div>', false);

        $this->actingAs($admin)
            ->get(route('admin.management.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Operations Table Management');
    }

    public function test_admin_management_resource_index_can_render_embedded_panel(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.management.resource.index', ['ranks', 'embedded' => 1]))
            ->assertOk()
            ->assertSee('Ranks')
            ->assertSee('Add Record')
            ->assertDontSee('id="efg-sidebar-navigation"', false);
    }

    public function test_member_cannot_open_admin_management(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $member = User::factory()->create();
        $member->assignRole('member');

        $this->actingAs($member)
            ->get(route('admin.management.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_update_archive_and_restore_a_managed_record(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('admin.management.store', 'onboarding-steps'), [
                'title' => 'Welcome Call',
                'description' => 'Complete the first sponsor welcome call.',
                'sort_order' => 10,
                'responsible_parties' => 'Self, TL',
                'notified_parties' => 'SP, TL',
                'is_active' => 1,
                'is_required' => 1,
            ])
            ->assertRedirect();

        $stepId = DB::table('onboarding_steps')->where('title', 'Welcome Call')->value('id');

        $this->assertNotNull($stepId);

        $this->actingAs($admin)
            ->patch(route('admin.management.update', ['onboarding-steps', $stepId]), [
                'title' => 'Welcome Strategy Call',
                'description' => 'Complete the first sponsor welcome call.',
                'sort_order' => 20,
                'responsible_parties' => 'Self, TL',
                'notified_parties' => 'SP, TL',
                'is_active' => 0,
                'is_required' => 1,
            ])
            ->assertRedirect(route('admin.management.edit', ['onboarding-steps', $stepId]));

        $this->assertDatabaseHas('onboarding_steps', [
            'id' => $stepId,
            'title' => 'Welcome Strategy Call',
            'sort_order' => 20,
            'responsible_parties' => 'Self, TL',
            'notified_parties' => 'SP, TL',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.management.status', ['onboarding-steps', $stepId]))
            ->assertRedirect();

        $this->assertTrue((bool) DB::table('onboarding_steps')->where('id', $stepId)->value('is_active'));

        $this->actingAs($admin)
            ->delete(route('admin.management.destroy', ['onboarding-steps', $stepId]))
            ->assertRedirect();

        $this->assertNotNull(DB::table('onboarding_steps')->where('id', $stepId)->value('deleted_at'));

        $this->actingAs($admin)
            ->patch(route('admin.management.restore', ['onboarding-steps', $stepId]))
            ->assertRedirect(route('admin.management.edit', ['onboarding-steps', $stepId]));

        $this->assertNull(DB::table('onboarding_steps')->where('id', $stepId)->value('deleted_at'));
    }

    public function test_agency_owner_can_manage_checklists_but_not_other_setup_tables(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $agencyOwner = User::factory()->create();
        $agencyOwner->assignRole('agency-owner');

        $this->actingAs($agencyOwner)
            ->get(route('admin.management.resource.index', 'onboarding-steps'))
            ->assertOk()
            ->assertSee('Onboarding Steps')
            ->assertSee('Add Item')
            ->assertSee('Update Seeder');

        $this->actingAs($agencyOwner)
            ->get(route('admin.management.resource.index', 'ranks'))
            ->assertForbidden();
    }

    public function test_trainer_can_view_but_not_edit_checklists(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $trainer = User::factory()->create();
        $trainer->assignRole('trainer');

        DB::table('cfm_training_modules')->insert([
            'title' => 'Read Only CFM Module',
            'description' => 'Visible but not editable.',
            'sort_order' => 10,
            'responsible_parties' => 'Self, SP, TR',
            'notified_parties' => 'SP, TR',
            'is_active' => true,
            'is_required' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $moduleId = DB::table('cfm_training_modules')->where('title', 'Read Only CFM Module')->value('id');

        $this->actingAs($trainer)
            ->get(route('admin.management.resource.index', 'cfm-training-modules'))
            ->assertOk()
            ->assertSee('Read Only CFM Module')
            ->assertSee('View')
            ->assertDontSee('Add Item')
            ->assertDontSee('Update Seeder')
            ->assertDontSee('Deactivate');

        $this->actingAs($trainer)
            ->get(route('admin.management.show', ['cfm-training-modules', $moduleId]))
            ->assertOk()
            ->assertSee('Visible but not editable.');

        $this->actingAs($trainer)
            ->get(route('admin.management.edit', ['cfm-training-modules', $moduleId]))
            ->assertForbidden();

        $this->actingAs($trainer)
            ->patch(route('admin.management.status', ['cfm-training-modules', $moduleId]))
            ->assertForbidden();
    }

    public function test_admin_can_manage_profile_completion_fields(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.management.resource.index', 'profile-completion-fields'))
            ->assertOk()
            ->assertSee('Profile Completion Fields')
            ->assertSee('Add Record');

        $this->actingAs($admin)
            ->post(route('admin.management.store', 'profile-completion-fields'), [
                'field_key' => 'phone',
                'label' => 'Phone number',
                'source' => 'profile',
                'sort_order' => 15,
                'is_active' => 1,
            ])
            ->assertRedirect();

        $fieldId = DB::table('profile_completion_fields')->where('field_key', 'phone')->value('id');

        $this->assertNotNull($fieldId);

        $this->actingAs($admin)
            ->patch(route('admin.management.update', ['profile-completion-fields', $fieldId]), [
                'field_key' => 'phone',
                'label' => 'Mobile phone',
                'source' => 'profile',
                'sort_order' => 25,
                'is_active' => 0,
            ])
            ->assertRedirect(route('admin.management.edit', ['profile-completion-fields', $fieldId]));

        $this->assertDatabaseHas('profile_completion_fields', [
            'id' => $fieldId,
            'label' => 'Mobile phone',
            'sort_order' => 25,
            'is_active' => false,
        ]);
    }

    public function test_admin_management_index_lists_notification_resources(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.management.index', ['search' => 'notification']))
            ->assertOk()
            ->assertSee('>notification_types</div>', false)
            ->assertSee('>notification_triggers</div>', false)
            ->assertSee('>notification_templates</div>', false)
            ->assertSee('>notifications</div>', false);
    }

    public function test_unknown_admin_management_resource_returns_not_found(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/management/not-a-real-table')
            ->assertNotFound();
    }
}
