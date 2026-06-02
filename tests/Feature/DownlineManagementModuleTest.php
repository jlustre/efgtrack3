<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\DownlineHierarchyService;
use Database\Seeders\DownlineManagementSeeder;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DownlineManagementModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_downline_pages_render_for_visible_team_members(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            TeamSeeder::class,
            DownlineManagementSeeder::class,
        ]);

        $owner = User::where('email', 'downline-owner@efgtrack.com')->firstOrFail();
        $member = User::where('sponsor_id', $owner->id)->firstOrFail();

        $this->actingAs($owner)
            ->get(route('team.index'))
            ->assertOk()
            ->assertSee('Team Command Center')
            ->assertSee('Rank Distribution');

        $this->actingAs($owner)
            ->get(route('team.tree'))
            ->assertOk()
            ->assertSee('Sponsor Tree')
            ->assertSee($member->name);

        $this->actingAs($owner)
            ->get(route('team.org-chart'))
            ->assertOk()
            ->assertSee('Executive Team Structure');

        $this->actingAs($owner)
            ->get(route('team.table', ['search' => $member->name]))
            ->assertOk()
            ->assertSee('Downline Member List')
            ->assertSee($member->name);

        $this->actingAs($owner)
            ->get(route('team.member', $member))
            ->assertOk()
            ->assertSee('Member Profile')
            ->assertSee($member->name);
    }

    public function test_unrelated_member_cannot_view_another_branch_member(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            TeamSeeder::class,
            DownlineManagementSeeder::class,
        ]);

        $target = User::where('email', 'avery.stone@efgtrack.com')->firstOrFail();
        $outsider = User::factory()->create(['name' => 'Outside Member']);
        $outsider->assignRole('member');

        app(DownlineHierarchyService::class)->rebuild();

        $this->actingAs($outsider)
            ->get(route('team.member', $target))
            ->assertForbidden();
    }

    public function test_tree_upline_button_stops_at_logged_in_user_boundary(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            TeamSeeder::class,
            DownlineManagementSeeder::class,
        ]);

        $owner = User::where('email', 'downline-owner@efgtrack.com')->firstOrFail();
        $directLeader = User::where('sponsor_id', $owner->id)->firstOrFail();
        $deeperMember = User::where('sponsor_id', $directLeader->id)->firstOrFail();

        $this->actingAs($owner)
            ->get(route('team.member.tree', $directLeader))
            ->assertOk()
            ->assertSee($deeperMember->name)
            ->assertDontSee('Go up one upline')
            ->assertSee('Show direct upline')
            ->assertSee('href="'.route('team.member.tree', $owner).'"', false);

        $this->actingAs($owner)
            ->get(route('team.tree'))
            ->assertOk()
            ->assertDontSee('Show direct upline')
            ->assertDontSee('Go up one upline')
            ->assertSee('Make this member the top card');
    }

    public function test_downline_export_requires_export_permission(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            TeamSeeder::class,
            DownlineManagementSeeder::class,
        ]);

        $owner = User::where('email', 'downline-owner@efgtrack.com')->firstOrFail();

        $this->actingAs($owner)
            ->get(route('team.export'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $member = User::where('email', 'farah.singh@efgtrack.com')->firstOrFail();

        $this->actingAs($member)
            ->get(route('team.export'))
            ->assertForbidden();
    }
}
