<?php

namespace Tests\Feature;

use App\Models\MentorAssignment;
use App\Models\User;
use App\Support\LocationOptions;
use Database\Seeders\ChecklistSeeder;
use Database\Seeders\ChecklistTypeSeeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\StartsChecklistTypes;
use Tests\TestCase;

class ChecklistTypeStartTest extends TestCase
{
    use RefreshDatabase;
    use StartsChecklistTypes;

    public function test_member_sees_not_started_page_until_ao_starts_checklist(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
            ChecklistTypeSeeder::class,
            ChecklistSeeder::class,
        ]);

        $member = $this->member();

        $this->actingAs($member)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('My Onboarding')
            ->assertSee('Licensing Tracker')
            ->assertSee('Field Apprenticeship')
            ->assertSee('CFM Training');

        $this->actingAs($member)
            ->get(route('onboarding.index'))
            ->assertOk()
            ->assertSee('Not started yet')
            ->assertDontSee('Overall Progress');
    }

    public function test_ao_can_start_onboarding_with_explicit_day_one_date(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
            ChecklistTypeSeeder::class,
            ChecklistSeeder::class,
        ]);

        $ao = User::factory()->create();
        $ao->assignRole('agency-owner');

        $member = $this->member(['sponsor_id' => $ao->id]);

        $this->actingAs($ao)
            ->post(route('team.member.checklist-type.start', ['user' => $member, 'typeCode' => 'onboarding']), [
                'started_at' => '2026-04-15',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('user_checklist_type_starts', [
            'user_id' => $member->id,
            'started_at' => '2026-04-15 00:00:00',
            'started_by' => $ao->id,
        ]);

        $this->actingAs($member)
            ->get(route('onboarding.index'))
            ->assertOk()
            ->assertSee('My Onboarding')
            ->assertDontSee('Not started yet');
    }

    public function test_cfm_can_start_checklist_for_assigned_trainee(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
            ChecklistTypeSeeder::class,
            ChecklistSeeder::class,
        ]);

        $cfm = User::factory()->create();
        $cfm->assignRole('certified-field-mentor');

        $member = $this->member(['mentor_id' => $cfm->id]);

        MentorAssignment::query()->create([
            'mentor_id' => $cfm->id,
            'apprentice_id' => $member->id,
            'assigned_by' => $cfm->id,
            'status' => 'active',
            'started_at' => now()->toDateString(),
        ]);

        $this->actingAs($cfm)
            ->post(route('team.member.checklist-type.start', ['user' => $member, 'typeCode' => 'licensing']), [
                'started_at' => '2026-05-01',
            ])
            ->assertRedirect();

        $this->actingAs($member)
            ->get(route('licensing.index'))
            ->assertOk()
            ->assertSee('Licensing Tracker')
            ->assertDontSee('Not started yet');
    }

    public function test_member_cannot_start_own_checklist(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
            ChecklistTypeSeeder::class,
        ]);

        $member = $this->member();

        $this->actingAs($member)
            ->post(route('team.member.checklist-type.start', ['user' => $member, 'typeCode' => 'onboarding']), [
                'started_at' => '2026-04-15',
            ])
            ->assertForbidden();
    }

    public function test_super_admin_can_start_own_licensing_from_tracker_page(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
            ChecklistTypeSeeder::class,
            ChecklistSeeder::class,
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('super-admin');
        $admin->profile()->create(LocationOptions::profileLocationIds('Canada'));

        $this->actingAs($admin)
            ->get(route('licensing.index'))
            ->assertOk()
            ->assertSee('Not started yet')
            ->assertSee('Start checklist');

        $this->actingAs($admin)
            ->post(route('team.member.checklist-type.start', ['user' => $admin, 'typeCode' => 'licensing']), [
                'started_at' => '2026-06-01',
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->get(route('licensing.index'))
            ->assertOk()
            ->assertSee('Licensing Checklist')
            ->assertDontSee('Not started yet');
    }

    private function member(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole('member');
        $user->profile()->create(array_merge([
            'is_efg_active_associate' => true,
            'efg_associate_id' => 'EFG-START-'.$user->id,
        ], LocationOptions::profileLocationIds('Canada')));

        return $user;
    }
}
