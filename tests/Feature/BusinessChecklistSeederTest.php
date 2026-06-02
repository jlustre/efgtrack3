<?php

namespace Tests\Feature;

use Database\Seeders\CfmTrainingModuleSeeder;
use Database\Seeders\FieldApprenticeshipProgramSeeder;
use Database\Seeders\LicensingStepSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BusinessChecklistSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_licensing_step_seeder_populates_relevant_steps(): void
    {
        $this->seed(LicensingStepSeeder::class);

        $this->assertDatabaseCount('licensing_steps', 13);
        $this->assertDatabaseHas('licensing_steps', [
            'title' => 'Confirm Licensing Jurisdiction',
            'sort_order' => 10,
            'responsible_parties' => 'Self, SP, CFM',
            'notified_parties' => 'SP, CFM',
            'is_required' => true,
        ]);
        $this->assertDatabaseHas('licensing_steps', [
            'title' => 'Receive License Approval',
            'responsible_parties' => 'Self, SP, AO, CFM',
            'notified_parties' => 'SP, AO, CFM',
            'is_required' => true,
        ]);
        $this->assertDatabaseHas('licensing_steps', [
            'title' => 'Complete Carrier Or Product Appointment Steps',
            'is_required' => false,
        ]);
    }

    public function test_field_apprenticeship_program_seeder_populates_program_and_steps(): void
    {
        $this->seed(FieldApprenticeshipProgramSeeder::class);

        $programId = DB::table('apprenticeship_programs')
            ->where('name', 'Field Apprenticeship Program')
            ->value('id');

        $this->assertNotNull($programId);
        $this->assertDatabaseCount('apprenticeship_programs', 1);
        $this->assertDatabaseCount('apprenticeship_steps', 14);
        $this->assertDatabaseHas('apprenticeship_steps', [
            'apprenticeship_program_id' => $programId,
            'title' => 'FAP Orientation With Sponsor And CFM',
            'sort_order' => 10,
            'responsible_parties' => 'Self, SP, CFM',
            'notified_parties' => 'SP, CFM',
        ]);
        $this->assertDatabaseHas('apprenticeship_steps', [
            'apprenticeship_program_id' => $programId,
            'title' => 'Receive FAP Approval',
            'sort_order' => 140,
            'responsible_parties' => 'SP, AO, CFM',
            'notified_parties' => 'SP, CFM, AO',
        ]);
    }

    public function test_cfm_training_module_seeder_populates_relevant_modules(): void
    {
        $this->seed(CfmTrainingModuleSeeder::class);

        $this->assertDatabaseCount('cfm_training_modules', 11);
        $this->assertDatabaseHas('cfm_training_modules', [
            'title' => 'CFM Role And Responsibility Orientation',
            'sort_order' => 10,
            'responsible_parties' => 'Self, SP, TR',
            'notified_parties' => 'SP, TR',
            'is_required' => true,
        ]);
        $this->assertDatabaseHas('cfm_training_modules', [
            'title' => 'CFM Certification Review',
            'responsible_parties' => 'SP, AO, TR',
            'notified_parties' => 'SP, AO, TR',
            'is_required' => true,
        ]);
        $this->assertDatabaseHas('cfm_training_modules', [
            'title' => 'Leadership Development Bonus Module',
            'is_required' => false,
        ]);
    }

    public function test_business_checklist_seeders_are_idempotent(): void
    {
        $this->seed([
            LicensingStepSeeder::class,
            FieldApprenticeshipProgramSeeder::class,
            CfmTrainingModuleSeeder::class,
        ]);
        $this->seed([
            LicensingStepSeeder::class,
            FieldApprenticeshipProgramSeeder::class,
            CfmTrainingModuleSeeder::class,
        ]);

        $this->assertDatabaseCount('licensing_steps', 13);
        $this->assertDatabaseCount('apprenticeship_programs', 1);
        $this->assertDatabaseCount('apprenticeship_steps', 14);
        $this->assertDatabaseCount('cfm_training_modules', 11);
        $this->assertSame(1, DB::table('licensing_steps')->where('title', 'Pass Licensing Exam')->count());
        $this->assertSame(1, DB::table('cfm_training_modules')->where('title', 'FAP Coaching Framework')->count());
    }
}
