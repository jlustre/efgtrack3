<?php

namespace Tests\Feature;

use App\Models\Prospect;
use App\Models\User;
use Database\Seeders\ProspectDemoSeeder;
use Database\Seeders\ProspectLookupSeeder;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProspectManagementModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_prospect_lookup_seeder_populates_crm_foundation_data(): void
    {
        $this->seed(ProspectLookupSeeder::class);

        $this->assertDatabaseHas('prospect_sources', ['name' => 'Social Media Lead']);
        $this->assertDatabaseHas('prospect_types', ['name' => 'Recruiting Prospect']);
        $this->assertDatabaseHas('prospect_interests', ['name' => 'Mortgage Protection']);
        $this->assertDatabaseHas('pipeline_stages', ['name' => 'Appointment Scheduled']);
        $this->assertDatabaseHas('communication_types', ['name' => 'Voicemail Left']);
        $this->assertDatabaseHas('appointment_types', ['name' => 'Mentor-Assisted Call']);
        $this->assertDatabaseHas('followup_statuses', ['name' => 'Overdue']);
        $this->assertDatabaseHas('prospect_share_permissions', ['key' => 'full_collaboration']);
    }

    public function test_prospect_management_page_is_available_to_members(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            ProspectLookupSeeder::class,
        ]);

        $user = User::factory()->create();
        $user->assignRole('member');

        $this->actingAs($user)
            ->get(route('team.prospects'))
            ->assertOk()
            ->assertSee('Prospect Management')
            ->assertSee('Private CRM workspace')
            ->assertSee('Pipeline Board')
            ->assertSee('Privacy Rules');
    }

    public function test_prospect_demo_seeder_creates_sample_prospects_and_dashboard_modules(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            TeamSeeder::class,
            ProspectLookupSeeder::class,
            ProspectDemoSeeder::class,
        ]);

        $owner = User::where('email', 'prospects@efgtrack.com')->firstOrFail();

        $this->assertGreaterThanOrEqual(50, Prospect::where('owner_id', $owner->id)->count());
        $this->assertDatabaseHas('prospect_followups', ['assigned_user_id' => $owner->id]);
        $this->assertDatabaseHas('prospect_appointments', ['owner_id' => $owner->id, 'status' => 'scheduled']);
        $this->assertDatabaseHas('prospect_imports', ['user_id' => $owner->id, 'file_name' => 'demo-prospects.csv']);

        $this->actingAs($owner)
            ->get(route('team.prospects'))
            ->assertOk()
            ->assertSee('Pipeline Summary')
            ->assertSee('Follow-Up Center')
            ->assertSee('Appointment Calendar')
            ->assertSee('Communication Timeline')
            ->assertSee('Shared By Me')
            ->assertSee('Import & Duplicates', false)
            ->assertSee('All Prospects')
            ->assertSee('Search name, email, phone, city...')
            ->assertSee('Avery Carter');
    }

    public function test_all_prospects_table_can_be_searched_and_filtered(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            TeamSeeder::class,
            ProspectLookupSeeder::class,
            ProspectDemoSeeder::class,
        ]);

        $owner = User::where('email', 'prospects@efgtrack.com')->firstOrFail();

        $this->actingAs($owner)
            ->get(route('team.prospects', ['prospect_search' => 'Avery']))
            ->assertOk()
            ->assertSee('All Prospects')
            ->assertSee('Avery Carter')
            ->assertSee('View prospect')
            ->assertSee('Edit prospect')
            ->assertSee('Archive prospect')
            ->assertSee('Delete prospect');

        $this->actingAs($owner)
            ->get(route('team.prospects', ['prospect_status' => 'archived']))
            ->assertOk()
            ->assertSee('All Prospects')
            ->assertSee('Archived');
    }

    public function test_all_prospect_row_actions_support_record_crud_workflow(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            TeamSeeder::class,
            ProspectLookupSeeder::class,
            ProspectDemoSeeder::class,
        ]);

        $owner = User::where('email', 'prospects@efgtrack.com')->firstOrFail();
        $prospect = Prospect::where('owner_id', $owner->id)->firstOrFail();

        $this->actingAs($owner)
            ->get(route('team.prospects.records.show', $prospect))
            ->assertOk()
            ->assertSee('Prospect Profile')
            ->assertSee($prospect->first_name);

        $this->actingAs($owner)
            ->get(route('team.prospects.records.edit', $prospect))
            ->assertOk()
            ->assertSee('Edit Prospect')
            ->assertSee('Save Changes');

        $stageId = DB::table('pipeline_stages')->where('slug', 'appointment-scheduled')->value('id');
        $sourceId = DB::table('prospect_sources')->where('slug', 'referral')->value('id');

        $this->actingAs($owner)
            ->patch(route('team.prospects.records.update', $prospect), [
                'first_name' => 'Updated',
                'last_name' => 'Prospect',
                'email' => 'updated.prospect@example.com',
                'phone' => '555-444-1212',
                'city' => 'Vancouver',
                'status' => 'active',
                'interest_level' => 'hot',
                'priority' => 'urgent',
                'pipeline_stage_id' => $stageId,
                'prospect_source_id' => $sourceId,
                'next_follow_up_at' => now()->addDay()->format('Y-m-d H:i:s'),
                'notes_summary' => 'Updated through the row action workflow.',
            ])
            ->assertRedirect(route('team.prospects.records.show', $prospect));

        $this->assertDatabaseHas('prospects', [
            'id' => $prospect->id,
            'first_name' => 'Updated',
            'priority' => 'urgent',
        ]);

        $this->actingAs($owner)
            ->patch(route('team.prospects.records.archive', $prospect))
            ->assertRedirect(route('team.prospects'));

        $prospect->refresh();

        $this->assertSame('archived', $prospect->status);
        $this->assertTrue($prospect->is_archived);
        $this->assertNotNull($prospect->archived_at);

        $this->actingAs($owner)
            ->delete(route('team.prospects.records.destroy', $prospect))
            ->assertRedirect(route('team.prospects'));

        $this->assertSoftDeleted('prospects', ['id' => $prospect->id]);
    }

    public function test_member_can_create_own_prospect(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            ProspectLookupSeeder::class,
        ]);

        $user = User::factory()->create();
        $user->assignRole('member');

        $this->actingAs($user)
            ->get(route('team.prospects.create'))
            ->assertOk()
            ->assertSee('Add Prospect')
            ->assertSee('Create Prospect')
            ->assertDontSee('Prospect Form Scaffold');

        $sourceId = DB::table('prospect_sources')->where('slug', 'warm-market')->value('id');

        $this->actingAs($user)
            ->post(route('team.prospects.store'), [
                'first_name' => 'Jamie',
                'last_name' => 'Prospect',
                'email' => 'jamie.prospect@example.com',
                'phone' => '6045550101',
                'city' => 'Toronto',
                'occupation' => 'Teacher',
                'status' => 'active',
                'interest_level' => 'warm',
                'priority' => 'medium',
                'prospect_source_id' => $sourceId,
                'notes_summary' => 'Met at networking event.',
            ])
            ->assertRedirect();

        $prospect = Prospect::query()->where('owner_id', $user->id)->firstOrFail();

        $this->assertSame('Jamie', $prospect->first_name);
        $this->assertSame($user->id, $prospect->owner_id);

        $this->actingAs($user)
            ->get(route('team.prospects.records.show', $prospect))
            ->assertOk()
            ->assertSee('Jamie Prospect');
    }

    public function test_prospect_shortcut_pages_render_module_tables(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            TeamSeeder::class,
            ProspectLookupSeeder::class,
            ProspectDemoSeeder::class,
        ]);

        $owner = User::where('email', 'prospects@efgtrack.com')->firstOrFail();

        foreach ([
            'create' => route('team.prospects.create'),
            'pipeline' => route('team.prospects.screen', 'pipeline'),
            'follow-ups' => route('team.prospects.screen', 'follow-ups'),
            'appointments' => route('team.prospects.screen', 'appointments'),
            'shared-with-me' => route('team.prospects.screen', 'shared-with-me'),
            'shared-by-me' => route('team.prospects.screen', 'shared-by-me'),
            'access-manager' => route('team.prospects.screen', 'access-manager'),
            'import' => route('team.prospects.screen', 'import'),
            'settings' => route('team.prospects.screen', 'settings'),
        ] as $screen => $url) {
            $expectedText = match ($screen) {
                'create' => 'Create Prospect',
                'pipeline' => 'Pipeline Prospect Table',
                'follow-ups' => 'Follow-Up Table',
                'appointments' => 'Appointment Table',
                'shared-with-me' => 'Shared With Me Table',
                'shared-by-me' => 'Shared By Me Table',
                'access-manager' => 'Access Manager Table',
                'import' => 'Import Batch Table',
                'settings' => 'Pipeline Stages',
            };

            $response = $this->actingAs($owner)->get($url);

            if ($screen === 'create') {
                $response->assertOk()->assertSee($expectedText);
            } else {
                $response->assertOk()->assertSee($expectedText)->assertSee('<table', false);
            }
        }
    }

    public function test_prospect_policy_allows_owner_and_active_share_only(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            ProspectLookupSeeder::class,
        ]);

        $owner = User::factory()->create();
        $owner->assignRole('member');

        $sharedUser = User::factory()->create();
        $sharedUser->assignRole('certified-field-mentor');

        $otherUser = User::factory()->create();
        $otherUser->assignRole('member');

        $stageId = DB::table('pipeline_stages')->where('slug', 'new-lead')->value('id');
        $sourceId = DB::table('prospect_sources')->where('slug', 'warm-market')->value('id');
        $permissionId = DB::table('prospect_share_permissions')->where('key', 'add_notes')->value('id');

        $prospect = Prospect::create([
            'owner_id' => $owner->id,
            'prospect_source_id' => $sourceId,
            'pipeline_stage_id' => $stageId,
            'first_name' => 'Prospect',
            'last_name' => 'Private',
            'email' => 'private.prospect@example.com',
            'interest_level' => 'hot',
            'priority' => 'high',
        ]);

        $this->assertTrue($owner->can('view', $prospect));
        $this->assertFalse($otherUser->can('view', $prospect));

        DB::table('prospect_shares')->insert([
            'prospect_id' => $prospect->id,
            'granted_by' => $owner->id,
            'shared_with' => $sharedUser->id,
            'prospect_share_permission_id' => $permissionId,
            'permission_level' => 'add_notes',
            'granted_at' => now(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertTrue($sharedUser->can('view', $prospect));

        DB::table('prospect_shares')
            ->where('prospect_id', $prospect->id)
            ->where('shared_with', $sharedUser->id)
            ->update([
                'status' => 'revoked',
                'revoked_at' => now(),
            ]);

        $this->assertFalse($sharedUser->fresh()->can('view', $prospect->fresh()));
    }

    public function test_prospect_activities_can_be_managed_from_api(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            ProspectLookupSeeder::class,
        ]);

        $owner = User::factory()->create();
        $owner->assignRole('member');

        $stageId = DB::table('pipeline_stages')->where('slug', 'new-lead')->value('id');
        $sourceId = DB::table('prospect_sources')->where('slug', 'warm-market')->value('id');

        $prospect = Prospect::create([
            'owner_id' => $owner->id,
            'prospect_source_id' => $sourceId,
            'pipeline_stage_id' => $stageId,
            'first_name' => 'Activity',
            'last_name' => 'Prospect',
            'email' => 'activity.prospect@example.com',
            'interest_level' => 'warm',
            'priority' => 'medium',
        ]);

        $this->actingAs($owner)
            ->getJson(route('team.prospects.activities.index', $prospect))
            ->assertOk()
            ->assertJson(['activities' => []]);

        $create = $this->actingAs($owner)
            ->postJson(route('team.prospects.activities.store', $prospect), [
                'activity_type' => 'call',
                'subject' => 'Intro call',
                'notes' => 'Discussed career opportunity.',
                'occurred_at' => now()->toIso8601String(),
                'outcome' => 'Interested',
                'next_action' => 'Send invite link',
                'next_follow_up_at' => now()->addDays(2)->toIso8601String(),
            ])
            ->assertCreated()
            ->assertJsonPath('activity.subject', 'Intro call');

        $activityId = $create->json('activity.id');

        $this->assertDatabaseHas('prospect_activities', [
            'id' => $activityId,
            'prospect_id' => $prospect->id,
            'subject' => 'Intro call',
        ]);

        $prospect->refresh();
        $this->assertNotNull($prospect->last_contacted_at);
        $this->assertNotNull($prospect->next_follow_up_at);

        $this->actingAs($owner)
            ->patchJson(route('team.prospects.activities.update', [$prospect, $activityId]), [
                'activity_type' => 'email',
                'subject' => 'Follow-up email',
                'notes' => 'Sent overview deck.',
                'occurred_at' => now()->toIso8601String(),
                'outcome' => 'Opened',
                'next_action' => 'Schedule meeting',
                'next_follow_up_at' => now()->addDays(3)->toIso8601String(),
            ])
            ->assertOk()
            ->assertJsonPath('activity.subject', 'Follow-up email');

        $this->actingAs($owner)
            ->get(route('team.prospects'))
            ->assertOk()
            ->assertSee('Activities', false);

        $this->actingAs($owner)
            ->deleteJson(route('team.prospects.activities.destroy', [$prospect, $activityId]))
            ->assertOk();

        $this->assertSoftDeleted('prospect_activities', ['id' => $activityId]);
    }
}
