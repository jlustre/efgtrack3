<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CfmManagementSeeder;
use Database\Seeders\CfmTrainingModuleSeeder;
use Database\Seeders\OnboardingStepSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TaskScenarioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CfmManagementPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_agency_owner_can_open_cfm_management_page(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            OnboardingStepSeeder::class,
        ]);

        $owner = User::factory()->create();
        $owner->assignRole('agency-owner');

        $this->actingAs($owner)
            ->get(route('team.cfms'))
            ->assertOk()
            ->assertSee('Certified Field Mentor Management', false)
            ->assertSee('Workload Legend', false)
            ->assertSee('Compare CFMs', false)
            ->assertSee('cfm-management-page', false);
    }

    public function test_agency_owner_can_update_cfm_licensed_jurisdictions(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            TaskScenarioSeeder::class,
            OnboardingStepSeeder::class,
            CfmTrainingModuleSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $owner = User::where('email', 'agency-owner@efgtrack.com')->firstOrFail();
        $cfm = User::where('email', 'cfm@efgtrack.com')->firstOrFail();

        $this->actingAs($owner)
            ->patch(route('team.cfms.licensed-jurisdictions.update', $cfm), [
                'licensed_jurisdictions' => ['Canada|Manitoba', 'United States|California'],
            ])
            ->assertRedirect(route('team.cfms', ['cfm' => $cfm->id]))
            ->assertSessionHas('cfm_licensed_feedback', fn (array $feedback) => $feedback['type'] === 'success');

        $cfm->refresh()->load('cfmMentorProfile');
        $this->assertSame(
            ['Canada|Manitoba', 'United States|California'],
            $cfm->cfmMentorProfile?->licensed_jurisdictions
        );

        $this->actingAs($owner)
            ->get(route('team.cfms', ['cfm' => $cfm->id]))
            ->assertOk()
            ->assertSee('Licensed jurisdictions', false)
            ->assertSee('MB, CA', false)
            ->assertSee('CA, US', false);
    }
}
