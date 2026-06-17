<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\ChecklistTypeSeeder;
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
        $this->seed(ChecklistTypeSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $typeId = DB::table('checklist_types')->where('code', 'onboarding')->value('id');

        $this->actingAs($admin)
            ->post(route('admin.management.store', 'checklists'), [
                'checklist_type_id' => $typeId,
                'title' => 'Welcome Call',
                'description' => 'Complete the first sponsor welcome call.',
                'sort_order' => 10,
                'responsible_parties' => 'Self, TL',
                'notified_parties' => 'SP, TL',
                'is_active' => 1,
                'is_required' => 1,
            ])
            ->assertRedirect();

        $stepId = DB::table('checklists')->where('title', 'Welcome Call')->value('id');

        $this->assertNotNull($stepId);

        $this->actingAs($admin)
            ->patch(route('admin.management.update', ['checklists', $stepId]), [
                'checklist_type_id' => $typeId,
                'title' => 'Welcome Strategy Call',
                'description' => 'Complete the first sponsor welcome call.',
                'sort_order' => 20,
                'responsible_parties' => 'Self, TL',
                'notified_parties' => 'SP, TL',
                'is_active' => 0,
                'is_required' => 1,
            ])
            ->assertRedirect(route('admin.management.edit', ['checklists', $stepId]));

        $this->assertDatabaseHas('checklists', [
            'id' => $stepId,
            'title' => 'Welcome Strategy Call',
            'sort_order' => 20,
            'responsible_parties' => 'Self, TL',
            'notified_parties' => 'SP, TL',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.management.status', ['checklists', $stepId]))
            ->assertRedirect();

        $this->assertTrue((bool) DB::table('checklists')->where('id', $stepId)->value('is_active'));

        $this->actingAs($admin)
            ->delete(route('admin.management.destroy', ['checklists', $stepId]))
            ->assertRedirect();

        $this->assertNotNull(DB::table('checklists')->where('id', $stepId)->value('deleted_at'));

        $this->actingAs($admin)
            ->patch(route('admin.management.restore', ['checklists', $stepId]))
            ->assertRedirect(route('admin.management.edit', ['checklists', $stepId]));

        $this->assertNull(DB::table('checklists')->where('id', $stepId)->value('deleted_at'));
    }

    public function test_agency_owner_can_manage_checklists_but_not_other_setup_tables(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $agencyOwner = User::factory()->create();
        $agencyOwner->assignRole('agency-owner');

        $this->actingAs($agencyOwner)
            ->get(route('admin.management.resource.index', 'checklists'))
            ->assertOk()
            ->assertSee('Checklists')
            ->assertSee('Add Record');

        $this->actingAs($agencyOwner)
            ->get(route('admin.management.resource.index', 'ranks'))
            ->assertForbidden();
    }

    public function test_trainer_can_view_but_not_edit_checklists(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $this->seed(ChecklistTypeSeeder::class);

        $trainer = User::factory()->create();
        $trainer->assignRole('trainer');

        $typeId = DB::table('checklist_types')->where('code', 'cfm-training')->value('id');

        DB::table('checklists')->insert([
            'checklist_type_id' => $typeId,
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

        $moduleId = DB::table('checklists')->where('title', 'Read Only CFM Module')->value('id');

        $this->actingAs($trainer)
            ->get(route('admin.management.resource.index', 'checklists'))
            ->assertOk()
            ->assertSee('Read Only CFM Module')
            ->assertSee('View')
            ->assertDontSee('Add Item')
            ->assertDontSee('Update Seeder')
            ->assertDontSee('Deactivate');

        $this->actingAs($trainer)
            ->get(route('admin.management.show', ['checklists', $moduleId]))
            ->assertOk()
            ->assertSee('Visible but not editable.');

        $this->actingAs($trainer)
            ->get(route('admin.management.edit', ['checklists', $moduleId]))
            ->assertForbidden();

        $this->actingAs($trainer)
            ->patch(route('admin.management.status', ['checklists', $moduleId]))
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

    public function test_admin_can_list_email_templates_page(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.management.resource.index', 'email-templates'))
            ->assertOk()
            ->assertSee('Email Templates')
            ->assertSee('Update Seeder')
            ->assertSee('Available merge tokens')
            ->assertSee('{{ member_name }}');
    }

    public function test_admin_can_update_email_template_seeder_from_list(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $seederPath = database_path('seeders/EmailTemplateSeeder.php');
        $originalSeeder = file_get_contents($seederPath);

        DB::table('email_templates')->insert([
            'key' => 'seed_export_test',
            'name' => 'Seed Export Test',
            'subject' => 'Subject for {{ member_name }}',
            'body' => '<p>Exported body for {{ app_name }}</p>',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $this->actingAs($admin)
                ->post(route('admin.management.update-seeder', 'email-templates'))
                ->assertRedirect(route('admin.management.resource.index', 'email-templates'))
                ->assertSessionHas('status', 'seeder-updated');

            $seeder = file_get_contents($seederPath);

            $this->assertStringContainsString('seed_export_test', $seeder);
            $this->assertStringContainsString('Seed Export Test', $seeder);
            $this->assertStringContainsString('Exported body for {{ app_name }}', $seeder);
            $this->assertStringContainsString('EmailTemplate::updateOrCreate', $seeder);
        } finally {
            file_put_contents($seederPath, $originalSeeder);
        }
    }

    public function test_admin_can_create_update_toggle_archive_and_restore_email_template(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('admin.management.store', 'email-templates'), [
                'key' => 'test_welcome',
                'name' => 'Test Welcome',
                'subject' => 'Welcome to {{ app_name }}',
                'body' => '<p>Hi {{ member_name }},</p><p>Welcome aboard.</p>',
                'is_active' => 1,
            ])
            ->assertRedirect();

        $templateId = DB::table('email_templates')->where('key', 'test_welcome')->value('id');

        $this->assertNotNull($templateId);

        $this->actingAs($admin)
            ->patch(route('admin.management.update', ['email-templates', $templateId]), [
                'key' => 'test_welcome',
                'name' => 'Test Welcome Updated',
                'subject' => 'Welcome aboard, {{ member_name }}',
                'body' => '<p>Hi {{ member_name }},</p><p>Glad you joined {{ app_name }}.</p>',
                'is_active' => 0,
            ])
            ->assertRedirect(route('admin.management.edit', ['email-templates', $templateId]));

        $this->assertDatabaseHas('email_templates', [
            'id' => $templateId,
            'name' => 'Test Welcome Updated',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.management.status', ['email-templates', $templateId]))
            ->assertRedirect();

        $this->assertTrue((bool) DB::table('email_templates')->where('id', $templateId)->value('is_active'));

        $this->actingAs($admin)
            ->delete(route('admin.management.destroy', ['email-templates', $templateId]))
            ->assertRedirect();

        $this->assertNotNull(DB::table('email_templates')->where('id', $templateId)->value('deleted_at'));

        $this->actingAs($admin)
            ->patch(route('admin.management.restore', ['email-templates', $templateId]))
            ->assertRedirect(route('admin.management.edit', ['email-templates', $templateId]));

        $this->assertNull(DB::table('email_templates')->where('id', $templateId)->value('deleted_at'));
    }

    public function test_agency_owner_cannot_access_email_templates(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $agencyOwner = User::factory()->create();
        $agencyOwner->assignRole('agency-owner');

        $this->actingAs($agencyOwner)
            ->get(route('admin.management.resource.index', 'email-templates'))
            ->assertForbidden();
    }
}
