<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\LocationOptions;
use Database\Seeders\CfmTrainingModuleSeeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\FieldApprenticeshipProgramSeeder;
use Database\Seeders\LicensingStepSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TrackerChecklistPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_tracker_pages_show_checklists_and_progress_stats(): void
    {
        $this->seedTrackerData();

        $user = $this->member();

        $this->actingAs($user)
            ->get(route('licensing.index'))
            ->assertOk()
            ->assertSee('Licensing Tracker')
            ->assertSee('Overall Progress')
            ->assertSee('Confirm Licensing Jurisdiction')
            ->assertSee('Responsible:')
            ->assertSee('Notify:');

        $this->actingAs($user)
            ->get(route('apprenticeship.index'))
            ->assertOk()
            ->assertSee('Field Apprenticeship')
            ->assertSee('FAP Orientation With Sponsor And CFM');

        $this->actingAs($user)
            ->get(route('cfm-training.index'))
            ->assertOk()
            ->assertSee('CFM Training')
            ->assertSee('CFM Role And Responsibility Orientation');
    }

    public function test_user_can_submit_and_reopen_tracker_items_for_confirmation(): void
    {
        $this->seedTrackerData();

        $user = $this->member();
        $stepId = DB::table('licensing_steps')
            ->where('title', 'Confirm Licensing Jurisdiction')
            ->value('id');

        $this->actingAs($user)
            ->patch(route('licensing.update', $stepId), ['completed' => '1'])
            ->assertRedirect();

        $this->assertDatabaseHas('user_licensing_progress', [
            'user_id' => $user->id,
            'licensing_step_id' => $stepId,
            'status' => 'pending_confirmation',
            'completed_at' => null,
        ]);

        $this->actingAs($user)
            ->patch(route('licensing.update', $stepId), ['completed' => '0'])
            ->assertRedirect();

        $this->assertDatabaseHas('user_licensing_progress', [
            'user_id' => $user->id,
            'licensing_step_id' => $stepId,
            'status' => 'not_started',
            'completed_at' => null,
        ]);
    }

    public function test_sponsor_can_confirm_or_reject_pending_tracker_items_with_comments(): void
    {
        $this->seedTrackerData();

        $sponsor = $this->member();
        $user = $this->member(['sponsor_id' => $sponsor->id]);

        $trackers = [
            [
                'step_table' => 'licensing_steps',
                'progress_table' => 'user_licensing_progress',
                'foreign_key' => 'licensing_step_id',
                'step_title' => 'Confirm Licensing Jurisdiction',
                'update_route' => 'licensing.update',
                'review_route' => 'licensing.review',
            ],
            [
                'step_table' => 'apprenticeship_steps',
                'progress_table' => 'user_apprenticeship_progress',
                'foreign_key' => 'apprenticeship_step_id',
                'step_title' => 'FAP Orientation With Sponsor And CFM',
                'update_route' => 'apprenticeship.update',
                'review_route' => 'apprenticeship.review',
            ],
            [
                'step_table' => 'cfm_training_modules',
                'progress_table' => 'cfm_training_progress',
                'foreign_key' => 'cfm_training_module_id',
                'step_title' => 'CFM Role And Responsibility Orientation',
                'update_route' => 'cfm-training.update',
                'review_route' => 'cfm-training.review',
            ],
        ];

        foreach ($trackers as $tracker) {
            $stepId = DB::table($tracker['step_table'])
                ->where('title', $tracker['step_title'])
                ->value('id');

            $this->actingAs($user)
                ->patch(route($tracker['update_route'], $stepId), ['completed' => '1'])
                ->assertRedirect();

            $progressId = DB::table($tracker['progress_table'])
                ->where('user_id', $user->id)
                ->where($tracker['foreign_key'], $stepId)
                ->value('id');

            $this->actingAs($sponsor)
                ->patch(route($tracker['review_route'], $progressId), [
                    'decision' => 'rejected',
                    'review_comments' => 'Please add proof before approval.',
                ])
                ->assertRedirect();

            $this->assertDatabaseHas($tracker['progress_table'], [
                'id' => $progressId,
                'status' => 'rejected',
                'reviewed_by' => $sponsor->id,
                'review_comments' => 'Please add proof before approval.',
                'completed_at' => null,
            ]);

            $this->actingAs($user)
                ->patch(route($tracker['update_route'], $stepId), ['completed' => '1'])
                ->assertRedirect();

            $this->actingAs($sponsor)
                ->patch(route($tracker['review_route'], $progressId), [
                    'decision' => 'confirmed',
                    'review_comments' => 'Confirmed.',
                ])
                ->assertRedirect();

            $this->assertDatabaseHas($tracker['progress_table'], [
                'id' => $progressId,
                'status' => 'completed',
                'reviewed_by' => $sponsor->id,
                'review_comments' => 'Confirmed.',
            ]);

            $this->assertNotNull(DB::table($tracker['progress_table'])->where('id', $progressId)->value('completed_at'));
        }
    }

    public function test_unnotified_user_cannot_review_pending_tracker_item(): void
    {
        $this->seedTrackerData();

        $user = $this->member();
        $otherUser = $this->member();

        $stepId = DB::table('licensing_steps')
            ->where('title', 'Confirm Licensing Jurisdiction')
            ->value('id');

        $this->actingAs($user)
            ->patch(route('licensing.update', $stepId), ['completed' => '1'])
            ->assertRedirect();

        $progressId = DB::table('user_licensing_progress')
            ->where('user_id', $user->id)
            ->where('licensing_step_id', $stepId)
            ->value('id');

        $this->actingAs($otherUser)
            ->patch(route('licensing.review', $progressId), [
                'decision' => 'confirmed',
                'review_comments' => 'Trying to approve.',
            ])
            ->assertForbidden();
    }

    private function seedTrackerData(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
            LicensingStepSeeder::class,
            FieldApprenticeshipProgramSeeder::class,
            CfmTrainingModuleSeeder::class,
        ]);
    }

    private function member(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole('member');
        $user->profile()->create(array_merge([
            'is_efg_active_associate' => true,
            'efg_associate_id' => 'EFG-TRACK-'.$user->id,
        ], LocationOptions::profileLocationIds('Canada')));

        return $user;
    }
}
