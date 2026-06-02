<?php

namespace Tests\Feature;

use App\Models\Rank;
use App\Models\RegistrationInvitation;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_user_with_role_rank_team_status_and_sponsor(): void
    {
        $this->seed([RankSeeder::class, RolePermissionSeeder::class]);

        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $sponsor = User::factory()->create();
        $team = Team::create(['name' => 'Alpha Team']);
        $rank = Rank::where('code', 'FA')->firstOrFail();

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'New Managed User',
                'email' => 'managed@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role' => 'member',
                'rank_id' => $rank->id,
                'team_id' => $team->id,
                'sponsor_id' => $sponsor->id,
                'is_active' => '1',
                'joined_at' => '2026-05-31',
            ]);

        $managedUser = User::where('email', 'managed@example.com')->firstOrFail();

        $response->assertRedirect(route('admin.users.edit', $managedUser));
        $this->assertTrue($managedUser->hasRole('member'));
        $this->assertSame($rank->id, $managedUser->rank_id);
        $this->assertSame($team->id, $managedUser->team_id);
        $this->assertSame($sponsor->id, $managedUser->sponsor_id);
        $this->assertTrue($managedUser->is_active);
        $this->assertSame('2026-05-31', $managedUser->joined_at->format('Y-m-d'));
    }

    public function test_agency_owner_can_update_user_role_rank_team_status_and_sponsor(): void
    {
        $this->seed([RankSeeder::class, RolePermissionSeeder::class]);

        $agencyOwner = User::factory()->create();
        $agencyOwner->assignRole('agency-owner');

        $managedUser = User::factory()->create();
        $managedUser->assignRole('member');

        $sponsor = User::factory()->create();
        $team = Team::create(['name' => 'Beta Team']);
        $rank = Rank::where('code', 'SFA')->firstOrFail();

        $response = $this
            ->actingAs($agencyOwner)
            ->patch(route('admin.users.update', $managedUser), [
                'name' => 'Updated Managed User',
                'email' => 'updated@example.com',
                'password' => '',
                'password_confirmation' => '',
                'role' => 'team-leader',
                'rank_id' => $rank->id,
                'team_id' => $team->id,
                'sponsor_id' => $sponsor->id,
                'is_active' => '0',
                'joined_at' => '2026-05-30',
            ]);

        $response->assertRedirect(route('admin.users.edit', $managedUser));

        $managedUser->refresh();

        $this->assertSame('Updated Managed User', $managedUser->name);
        $this->assertSame('updated@example.com', $managedUser->email);
        $this->assertTrue($managedUser->hasRole('team-leader'));
        $this->assertSame($rank->id, $managedUser->rank_id);
        $this->assertSame($team->id, $managedUser->team_id);
        $this->assertSame($sponsor->id, $managedUser->sponsor_id);
        $this->assertFalse($managedUser->is_active);
        $this->assertSame('2026-05-30', $managedUser->joined_at->format('Y-m-d'));
    }

    public function test_admin_can_soft_delete_and_restore_user(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $managedUser = User::factory()->create();
        $managedUser->profile()->create(['efg_associate_id' => 'SOFT-DELETE-1']);
        $invitation = RegistrationInvitation::factory()->for($managedUser, 'sponsor')->create();

        $this
            ->actingAs($admin)
            ->delete(route('admin.users.destroy', $managedUser))
            ->assertRedirect(route('admin.users.index', ['trashed' => 'with']));

        $this->assertSoftDeleted('users', ['id' => $managedUser->id]);
        $this->assertSoftDeleted('profiles', ['user_id' => $managedUser->id]);
        $this->assertSoftDeleted('registration_invitations', ['id' => $invitation->id]);

        $this
            ->actingAs($admin)
            ->patch(route('admin.users.restore', $managedUser->id))
            ->assertRedirect(route('admin.users.edit', $managedUser));

        $this->assertNotSoftDeleted('users', ['id' => $managedUser->id]);
    }

    public function test_admin_cannot_self_deactivate_or_self_delete(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $this
            ->actingAs($admin)
            ->patch(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'email' => $admin->email,
                'password' => '',
                'password_confirmation' => '',
                'role' => 'super-admin',
                'rank_id' => null,
                'team_id' => null,
                'sponsor_id' => null,
                'is_active' => '0',
                'joined_at' => now()->format('Y-m-d'),
            ])
            ->assertSessionHasErrors('is_active');

        $this
            ->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertSessionHasErrors('user');

        $this->assertNotSoftDeleted('users', ['id' => $admin->id]);
    }

    public function test_team_leader_cannot_access_user_management_crud(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $teamLeader = User::factory()->create();
        $teamLeader->assignRole('team-leader');

        $this
            ->actingAs($teamLeader)
            ->get(route('admin.users.create'))
            ->assertForbidden();
    }
}
