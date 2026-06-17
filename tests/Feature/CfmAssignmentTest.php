<?php

namespace Tests\Feature;

use App\Models\MentorAssignment;
use App\Models\User;
use Database\Seeders\CfmManagementSeeder;
use Database\Seeders\ChecklistSeeder;
use Database\Seeders\ChecklistTypeSeeder;
use Database\Seeders\EmailTemplateSeeder;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TaskScenarioSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CfmAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_agency_owner_can_assign_associate_to_cfm(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            EmailTemplateSeeder::class,
            TeamSeeder::class,
            ChecklistTypeSeeder::class,
            ChecklistSeeder::class,
            TaskScenarioSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $agencyOwner = User::where('email', 'agency-owner@efgtrack.com')->firstOrFail();
        $cfm = User::where('email', 'maria.cfm@efgtrack.com')->firstOrFail();
        $associate = User::where('email', 'fap.queue1@example.com')->firstOrFail();

        $this->actingAs($agencyOwner)
            ->postJson(route('team.cfms.assign'), [
                'associate_id' => $associate->id,
                'cfm_id' => $cfm->id,
                'reason' => 'Ready for FAP mentorship',
                'start_date' => now()->toDateString(),
                'notes' => 'Seeded assignment test',
            ])
            ->assertOk()
            ->assertJsonPath('status', 'pending');

        $associate->refresh();
        $this->assertNull($associate->mentor_id);

        $this->assertDatabaseHas('mentor_assignments', [
            'mentor_id' => $cfm->id,
            'apprentice_id' => $associate->id,
            'status' => 'pending',
        ]);
    }

    private function confirmAssignmentFor(User $cfm, User $associate): void
    {
        $assignment = MentorAssignment::query()
            ->where('mentor_id', $cfm->id)
            ->where('apprentice_id', $associate->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $this->actingAs($cfm)
            ->post(route('cfm.portal.assignments.confirm', $assignment))
            ->assertRedirect(route('cfm.portal'));
    }

    public function test_assignment_requires_cfm_approval_when_requested(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            EmailTemplateSeeder::class,
            TeamSeeder::class,
            ChecklistTypeSeeder::class,
            ChecklistSeeder::class,
            TaskScenarioSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $agencyOwner = User::where('email', 'agency-owner@efgtrack.com')->firstOrFail();
        $cfm = User::where('email', 'maria.cfm@efgtrack.com')->firstOrFail();
        $associate = User::where('email', 'fap.queue2@example.com')->firstOrFail();

        $this->actingAs($agencyOwner)
            ->postJson(route('team.cfms.assign'), [
                'associate_id' => $associate->id,
                'cfm_id' => $cfm->id,
                'require_cfm_approval' => true,
            ])
            ->assertOk()
            ->assertJsonPath('status', 'pending');

        $associate->refresh();
        $this->assertNull($associate->mentor_id);

        $this->assertSame(1, MentorAssignment::query()
            ->where('mentor_id', $cfm->id)
            ->where('apprentice_id', $associate->id)
            ->where('status', 'pending')
            ->count());
    }

    public function test_agency_owner_can_assign_associate_via_web_form(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            EmailTemplateSeeder::class,
            TeamSeeder::class,
            ChecklistTypeSeeder::class,
            ChecklistSeeder::class,
            TaskScenarioSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $agencyOwner = User::where('email', 'agency-owner@efgtrack.com')->firstOrFail();
        $cfm = User::where('email', 'maria.cfm@efgtrack.com')->firstOrFail();
        $associate = User::where('email', 'fap.queue1@example.com')->firstOrFail();

        $this->actingAs($agencyOwner)
            ->post(route('team.cfms.assign'), [
                'associate_id' => $associate->id,
                'cfm_id' => $cfm->id,
                'reason' => 'Ready for mentorship',
            ])
            ->assertRedirect(route('team.cfms'))
            ->assertSessionHas('status');

        $this->confirmAssignmentFor($cfm, $associate);

        $associate->refresh();
        $this->assertSame($cfm->id, $associate->mentor_id);
    }

    public function test_cannot_assign_cfm_not_licensed_in_associate_jurisdiction(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            EmailTemplateSeeder::class,
            TeamSeeder::class,
            ChecklistTypeSeeder::class,
            ChecklistSeeder::class,
            TaskScenarioSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $agencyOwner = User::where('email', 'agency-owner@efgtrack.com')->firstOrFail();
        $celeste = User::where('email', 'cfm@efgtrack.com')->firstOrFail();
        $associate = User::where('email', 'fap.queue4@example.com')->firstOrFail();

        $this->actingAs($agencyOwner)
            ->postJson(route('team.cfms.assign'), [
                'associate_id' => $associate->id,
                'cfm_id' => $celeste->id,
                'reason' => 'Should be blocked by jurisdiction rule',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['cfm_id']);

        $associate->refresh();
        $this->assertNull($associate->mentor_id);
    }

    public function test_can_assign_when_cfm_is_licensed_in_associate_jurisdiction(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            EmailTemplateSeeder::class,
            TeamSeeder::class,
            ChecklistTypeSeeder::class,
            ChecklistSeeder::class,
            TaskScenarioSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $agencyOwner = User::where('email', 'agency-owner@efgtrack.com')->firstOrFail();
        $maria = User::where('email', 'maria.cfm@efgtrack.com')->firstOrFail();
        $associate = User::where('email', 'fap.queue2@example.com')->firstOrFail();

        $this->actingAs($agencyOwner)
            ->postJson(route('team.cfms.assign'), [
                'associate_id' => $associate->id,
                'cfm_id' => $maria->id,
            ])
            ->assertOk();

        $this->confirmAssignmentFor($maria, $associate);

        $associate->refresh();
        $this->assertSame($maria->id, $associate->mentor_id);
    }

    public function test_can_assign_us_associate_to_us_licensed_cfm(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            EmailTemplateSeeder::class,
            TeamSeeder::class,
            ChecklistTypeSeeder::class,
            ChecklistSeeder::class,
            TaskScenarioSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $agencyOwner = User::where('email', 'agency-owner@efgtrack.com')->firstOrFail();
        $james = User::where('email', 'james.cfm@efgtrack.com')->firstOrFail();
        $associate = User::where('email', 'fap.queue.us-tx@example.com')->firstOrFail();

        $this->actingAs($agencyOwner)
            ->postJson(route('team.cfms.assign'), [
                'associate_id' => $associate->id,
                'cfm_id' => $james->id,
            ])
            ->assertOk();

        $this->confirmAssignmentFor($james, $associate);

        $associate->refresh();
        $this->assertSame($james->id, $associate->mentor_id);
    }

    public function test_cannot_assign_us_associate_to_unlicensed_us_cfm(): void
    {
        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            EmailTemplateSeeder::class,
            TeamSeeder::class,
            ChecklistTypeSeeder::class,
            ChecklistSeeder::class,
            TaskScenarioSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $agencyOwner = User::where('email', 'agency-owner@efgtrack.com')->firstOrFail();
        $lisa = User::where('email', 'lisa.cfm@efgtrack.com')->firstOrFail();
        $associate = User::where('email', 'fap.queue.us-tx@example.com')->firstOrFail();

        $this->actingAs($agencyOwner)
            ->postJson(route('team.cfms.assign'), [
                'associate_id' => $associate->id,
                'cfm_id' => $lisa->id,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['cfm_id']);
    }
}
