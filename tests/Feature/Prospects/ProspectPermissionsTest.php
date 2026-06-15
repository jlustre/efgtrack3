<?php

namespace Tests\Feature\Prospects;

use App\Livewire\Prospects\ProspectPermissions;
use App\Models\Prospect;
use App\Models\User;
use Database\Seeders\ProspectLookupSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ProspectPermissionsTest extends TestCase
{
    use RefreshDatabase;

    private function seedBase(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            ProspectLookupSeeder::class,
        ]);
    }

    private function makeProspect(User $owner): Prospect
    {
        $stageId = DB::table('pipeline_stages')->where('slug', 'new-lead')->value('id');
        $sourceId = DB::table('prospect_sources')->where('slug', 'warm-market')->value('id');

        return Prospect::create([
            'owner_id' => $owner->id,
            'prospect_source_id' => $sourceId,
            'pipeline_stage_id' => $stageId,
            'first_name' => 'Access',
            'last_name' => 'Manager',
            'interest_level' => 'warm',
            'priority' => 'medium',
        ]);
    }

    public function test_access_manager_page_renders_for_owner(): void
    {
        $this->seedBase();

        $owner = User::factory()->create();
        $owner->assignRole('member');

        $this->actingAs($owner)
            ->get(route('team.prospects.access-manager'))
            ->assertOk()
            ->assertSee('Access Manager')
            ->assertSee('Shares Granted By You')
            ->assertSee('Access Log');
    }

    public function test_owner_can_revoke_share_from_access_manager(): void
    {
        $this->seedBase();

        $owner = User::factory()->create();
        $owner->assignRole('member');

        $collaborator = User::factory()->create();
        $collaborator->assignRole('member');

        $prospect = $this->makeProspect($owner);
        $permissionId = DB::table('prospect_share_permissions')->where('key', 'view_only')->value('id');

        $shareId = DB::table('prospect_shares')->insertGetId([
            'prospect_id' => $prospect->id,
            'granted_by' => $owner->id,
            'shared_with' => $collaborator->id,
            'prospect_share_permission_id' => $permissionId,
            'permission_level' => 'view_only',
            'granted_at' => now(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::actingAs($owner)
            ->test(ProspectPermissions::class)
            ->call('revokeShare', $shareId)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('prospect_shares', [
            'id' => $shareId,
            'status' => 'revoked',
        ]);
    }
}
