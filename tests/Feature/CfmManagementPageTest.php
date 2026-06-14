<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CfmManagementService;
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
            ->assertSee('Manitoba, Canada', false)
            ->assertSee('California, United States', false);
    }

    public function test_payload_includes_computed_recommendations_for_seeded_fap_associate(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            TaskScenarioSeeder::class,
            OnboardingStepSeeder::class,
            CfmTrainingModuleSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $owner = User::where('email', 'agency-owner@efgtrack.com')->firstOrFail();
        $ontarioAssociate = User::where('email', 'fap.queue1@example.com')->firstOrFail();

        $payload = app(CfmManagementService::class)->payloadFor($owner);

        $this->assertArrayHasKey('recommendationsByAssociate', $payload);
        $this->assertArrayHasKey($ontarioAssociate->id, $payload['recommendationsByAssociate']);
        $this->assertNotNull($payload['defaultRecommendationAssociateId']);
        $this->assertArrayHasKey(
            $payload['defaultRecommendationAssociateId'],
            $payload['recommendationsByAssociate']
        );

        $recommendations = $payload['recommendationsByAssociate'][$ontarioAssociate->id];
        $cards = array_values(array_filter($recommendations, fn (array $row) => ! empty($row['cfmName'])));

        $this->assertNotEmpty($cards);

        $mariaCard = collect($cards)->firstWhere('cfmName', 'Maria Santos');
        $this->assertNotNull($mariaCard);
        $this->assertContains($mariaCard['statusLabel'], ['Recommended', 'Use Caution']);
        $this->assertGreaterThan(0, $mariaCard['fitScore']);

        $johnCard = collect($cards)->firstWhere('cfmName', 'John Reyes');
        $this->assertNull($johnCard);

        $this->actingAs($owner)
            ->get(route('team.cfms'))
            ->assertOk()
            ->assertSee('Smart Recommendations', false)
            ->assertSee('Maria Santos', false)
            ->assertSee('Owen Taylor', false);
    }
}
