<?php

namespace Tests\Feature;

use App\Models\OnboardingStep;
use Database\Seeders\OnboardingStepSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OnboardingStepSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_onboarding_step_seeder_populates_business_relevant_checklist_items(): void
    {
        $this->seed(OnboardingStepSeeder::class);

        $this->assertDatabaseCount('onboarding_steps', 19);

        $this->assertDatabaseHas('onboarding_steps', [
            'title' => 'Complete Member Profile',
            'sort_order' => 10,
            'responsible_parties' => 'Self',
            'notified_parties' => 'SP',
            'is_required' => true,
        ]);

        $this->assertDatabaseHas('onboarding_steps', [
            'title' => 'Receive Certified Field Mentor Assignment',
            'responsible_parties' => 'SP, AO',
            'notified_parties' => 'SP, CFM, AO',
            'is_required' => true,
        ]);

        $this->assertDatabaseHas('onboarding_steps', [
            'title' => 'Review Rank Advancement Path',
            'is_required' => false,
        ]);

        $this->assertDatabaseHas('onboarding_steps', [
            'title' => 'Canada: Review Provincial Licensing Path',
            'country' => 'Canada',
            'responsible_parties' => 'Self, SP, CFM',
            'notified_parties' => 'SP, CFM',
            'is_required' => true,
        ]);
    }

    public function test_onboarding_step_seeder_is_idempotent(): void
    {
        $this->seed(OnboardingStepSeeder::class);
        $this->seed(OnboardingStepSeeder::class);

        $this->assertDatabaseCount('onboarding_steps', 19);
        $this->assertSame(1, DB::table('onboarding_steps')->where('title', 'Start Licensing Tracker')->count());
    }

    public function test_onboarding_steps_can_be_filtered_by_member_country(): void
    {
        $this->seed(OnboardingStepSeeder::class);

        $canadaSteps = OnboardingStep::query()
            ->applicableToCountry('Canada')
            ->pluck('title');

        $this->assertTrue($canadaSteps->contains('Complete Member Profile'));
        $this->assertTrue($canadaSteps->contains('Canada: Review Provincial Licensing Path'));
        $this->assertFalse($canadaSteps->contains('United States: Review State Licensing Path'));

        $usSteps = OnboardingStep::query()
            ->applicableToCountry('United States')
            ->pluck('title');

        $this->assertTrue($usSteps->contains('United States: Review State Licensing Path'));
        $this->assertFalse($usSteps->contains('Canada: Review Provincial Licensing Path'));
    }
}
