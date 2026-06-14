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
            ->assertSee('Operations Table Management');

        $this->actingAs($admin)
            ->get(route('admin.management.index'))
            ->assertOk()
            ->assertSee('Operations Table Management');
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
