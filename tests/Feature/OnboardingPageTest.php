<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\LocationOptions;
use Database\Seeders\CountrySeeder;
use Database\Seeders\OnboardingStepSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OnboardingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_onboarding_page_shows_applicable_checklist_and_progress_stats(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
            OnboardingStepSeeder::class,
        ]);

        $user = User::factory()->create();
        $user->assignRole('member');
        $user->profile()->create(array_merge([
            'is_efg_active_associate' => true,
            'efg_associate_id' => 'EFG-ONB-1',
        ], LocationOptions::profileLocationIds('Canada')));

        $profileStepId = DB::table('onboarding_steps')
            ->where('title', 'Complete Member Profile')
            ->value('id');

        DB::table('user_onboarding_progress')->insert([
            'user_id' => $user->id,
            'onboarding_step_id' => $profileStepId,
            'status' => 'completed',
            'completed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('onboarding.index'))
            ->assertOk()
            ->assertSee('My Onboarding')
            ->assertSee('Overall Progress')
            ->assertSee('1/16')
            ->assertSee('Canada: Review Provincial Licensing Path')
            ->assertDontSee('United States: Review State Licensing Path')
            ->assertSee('Responsible:')
            ->assertSee('Notify:');
    }

    public function test_user_can_submit_and_reopen_an_onboarding_item_for_confirmation(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
            OnboardingStepSeeder::class,
        ]);

        $user = User::factory()->create();
        $user->assignRole('member');
        $user->profile()->create(array_merge([
            'is_efg_active_associate' => true,
            'efg_associate_id' => 'EFG-ONB-2',
        ], LocationOptions::profileLocationIds('Canada')));

        $stepId = DB::table('onboarding_steps')
            ->where('title', 'Complete Member Profile')
            ->value('id');

        $this->actingAs($user)
            ->patch(route('onboarding.update', $stepId), ['completed' => '1'])
            ->assertRedirect();

        $this->assertDatabaseHas('user_onboarding_progress', [
            'user_id' => $user->id,
            'onboarding_step_id' => $stepId,
            'status' => 'pending_confirmation',
            'completed_at' => null,
        ]);

        $this->actingAs($user)
            ->patch(route('onboarding.update', $stepId), ['completed' => '0'])
            ->assertRedirect();

        $this->assertDatabaseHas('user_onboarding_progress', [
            'user_id' => $user->id,
            'onboarding_step_id' => $stepId,
            'status' => 'not_started',
            'completed_at' => null,
        ]);
    }

    public function test_notified_sponsor_can_confirm_or_reject_pending_onboarding_item_with_comments(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
            OnboardingStepSeeder::class,
        ]);

        $sponsor = User::factory()->create();
        $sponsor->assignRole('member');

        $user = User::factory()->create([
            'sponsor_id' => $sponsor->id,
        ]);
        $user->assignRole('member');
        $user->profile()->create(array_merge([
            'is_efg_active_associate' => true,
            'efg_associate_id' => 'EFG-ONB-4',
        ], LocationOptions::profileLocationIds('Canada')));

        $stepId = DB::table('onboarding_steps')
            ->where('title', 'Complete Member Profile')
            ->value('id');

        $this->actingAs($user)
            ->patch(route('onboarding.update', $stepId), ['completed' => '1'])
            ->assertRedirect();

        $progressId = DB::table('user_onboarding_progress')
            ->where('user_id', $user->id)
            ->where('onboarding_step_id', $stepId)
            ->value('id');

        $this->actingAs($sponsor)
            ->get(route('onboarding.index'))
            ->assertOk()
            ->assertDontSee('Needs Your Confirmation');

        $this->actingAs($sponsor)
            ->get(route('tasks.index'))
            ->assertOk()
            ->assertSee('My Tasks')
            ->assertSee('Complete Member Profile')
            ->assertSee('Reject')
            ->assertSee('Confirm');

        $this->actingAs($sponsor)
            ->patch(route('onboarding.review', $progressId), [
                'decision' => 'rejected',
                'review_comments' => 'Please finish the profile photo first.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('user_onboarding_progress', [
            'id' => $progressId,
            'status' => 'rejected',
            'reviewed_by' => $sponsor->id,
            'review_comments' => 'Please finish the profile photo first.',
            'completed_at' => null,
        ]);

        $this->actingAs($user)
            ->patch(route('onboarding.update', $stepId), ['completed' => '1'])
            ->assertRedirect();

        $this->actingAs($sponsor)
            ->patch(route('onboarding.review', $progressId), [
                'decision' => 'confirmed',
                'review_comments' => 'Confirmed.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('user_onboarding_progress', [
            'id' => $progressId,
            'status' => 'completed',
            'reviewed_by' => $sponsor->id,
            'review_comments' => 'Confirmed.',
        ]);

        $this->assertNotNull(DB::table('user_onboarding_progress')->where('id', $progressId)->value('completed_at'));
    }

    public function test_unnotified_user_cannot_review_pending_onboarding_item(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
            OnboardingStepSeeder::class,
        ]);

        $user = User::factory()->create();
        $user->assignRole('member');
        $user->profile()->create(array_merge([
            'is_efg_active_associate' => true,
            'efg_associate_id' => 'EFG-ONB-5',
        ], LocationOptions::profileLocationIds('Canada')));

        $otherUser = User::factory()->create();
        $otherUser->assignRole('member');

        $stepId = DB::table('onboarding_steps')
            ->where('title', 'Complete Member Profile')
            ->value('id');

        $this->actingAs($user)
            ->patch(route('onboarding.update', $stepId), ['completed' => '1'])
            ->assertRedirect();

        $progressId = DB::table('user_onboarding_progress')
            ->where('user_id', $user->id)
            ->where('onboarding_step_id', $stepId)
            ->value('id');

        $this->actingAs($otherUser)
            ->patch(route('onboarding.review', $progressId), [
                'decision' => 'confirmed',
                'review_comments' => 'Trying to approve.',
            ])
            ->assertForbidden();
    }

    public function test_user_cannot_complete_country_specific_item_that_does_not_apply(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
            OnboardingStepSeeder::class,
        ]);

        $user = User::factory()->create();
        $user->assignRole('member');
        $user->profile()->create(array_merge([
            'is_efg_active_associate' => true,
            'efg_associate_id' => 'EFG-ONB-3',
        ], LocationOptions::profileLocationIds('Canada')));

        $usStepId = DB::table('onboarding_steps')
            ->where('title', 'United States: Review State Licensing Path')
            ->value('id');

        $this->actingAs($user)
            ->patch(route('onboarding.update', $usStepId), ['completed' => '1'])
            ->assertNotFound();
    }
}
