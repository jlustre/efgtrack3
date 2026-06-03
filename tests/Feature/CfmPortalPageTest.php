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

class CfmPortalPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_cfm_can_open_cfm_portal(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            TaskScenarioSeeder::class,
            OnboardingStepSeeder::class,
            CfmTrainingModuleSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $cfm = User::where('email', 'cfm@efgtrack.com')->firstOrFail();

        $this->actingAs($cfm)
            ->get(route('cfm.portal'))
            ->assertOk()
            ->assertSee('CFM Portal', false)
            ->assertSee('Celeste Navarro', false)
            ->assertSee('My Trainees', false)
            ->assertSee('CFM Training Progress', false)
            ->assertSee('Agency Owner', false)
            ->assertSee('Arielle Morgan', false)
            ->assertSee('Edit', false);
    }

    public function test_cfm_can_update_portal_profile(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            TaskScenarioSeeder::class,
            OnboardingStepSeeder::class,
            CfmTrainingModuleSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $cfm = User::where('email', 'cfm@efgtrack.com')->firstOrFail();

        $this->actingAs($cfm)
            ->patch(route('cfm.portal.profile.update'), [
                'phone' => '555-0100',
                'city' => 'Toronto',
                'province' => 'Ontario',
                'country' => 'Canada',
                'timezone' => 'Canada Eastern Time',
                'languages' => 'English, French',
                'specialties' => 'Field Apprenticeship, Licensing',
                'licensed_jurisdictions' => ['Canada|Ontario', 'Canada|Quebec'],
                'mentor_bio' => 'Updated mentor bio for testing.',
                'manual_unavailable' => '1',
            ])
            ->assertRedirect(route('cfm.portal'))
            ->assertSessionHas('profile_feedback', fn (array $feedback) => $feedback['type'] === 'success');

        $cfm->refresh()->load(['profile', 'cfmMentorProfile']);

        $this->assertSame('555-0100', $cfm->profile?->phone);
        $this->assertSame('Toronto', $cfm->profile?->city);
        $this->assertSame('Updated mentor bio for testing.', $cfm->cfmMentorProfile?->mentor_bio);
        $this->assertSame(['English', 'French'], $cfm->cfmMentorProfile?->languages);
        $this->assertTrue($cfm->cfmMentorProfile?->manual_unavailable);
        $this->assertSame(['Canada|Ontario', 'Canada|Quebec'], $cfm->cfmMentorProfile?->licensed_jurisdictions);
    }

    public function test_cfm_portal_displays_licensed_jurisdictions(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            TaskScenarioSeeder::class,
            OnboardingStepSeeder::class,
            CfmTrainingModuleSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $cfm = User::where('email', 'cfm@efgtrack.com')->firstOrFail();

        $this->actingAs($cfm)
            ->get(route('cfm.portal'))
            ->assertOk()
            ->assertSee('Licensed jurisdictions', false)
            ->assertSee('ON, CA', false)
            ->assertSee('BC, CA', false);
    }

    public function test_cfm_profile_update_shows_validation_errors_in_modal(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            TaskScenarioSeeder::class,
            OnboardingStepSeeder::class,
            CfmTrainingModuleSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $cfm = User::where('email', 'cfm@efgtrack.com')->firstOrFail();

        $this->actingAs($cfm)
            ->from(route('cfm.portal'))
            ->patch(route('cfm.portal.profile.update'), [
                'country' => 'Canada',
                'province' => 'Invalid Province',
                'timezone' => 'Not A Timezone',
            ])
            ->assertRedirect(route('cfm.portal'))
            ->assertSessionHas('open_edit_profile_modal', true)
            ->assertSessionHas('profile_feedback', fn (array $feedback) => $feedback['type'] === 'error')
            ->assertSessionHasErrors(['province', 'timezone']);

        $this->actingAs($cfm)
            ->get(route('cfm.portal'))
            ->assertOk()
            ->assertSee('Could not save profile', false)
            ->assertSee('Edit CFM Profile', false)
            ->assertSee('Save Profile', false);
    }

    public function test_cfm_profile_update_shows_success_message(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            TaskScenarioSeeder::class,
            OnboardingStepSeeder::class,
            CfmTrainingModuleSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $cfm = User::where('email', 'cfm@efgtrack.com')->firstOrFail();

        $this->actingAs($cfm)
            ->patch(route('cfm.portal.profile.update'), [
                'phone' => '555-0199',
                'city' => 'Vancouver',
                'province' => 'British Columbia',
                'country' => 'Canada',
                'timezone' => 'PST',
            ])
            ->assertRedirect(route('cfm.portal'));

        $this->actingAs($cfm)
            ->get(route('cfm.portal'))
            ->assertOk()
            ->assertSee('Profile saved', false)
            ->assertSee('Your CFM profile was updated successfully.', false);
    }

    public function test_admin_cannot_update_cfm_portal_profile(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            TaskScenarioSeeder::class,
            OnboardingStepSeeder::class,
            CfmTrainingModuleSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->patch(route('cfm.portal.profile.update'), [
                'phone' => '555-0100',
                'mentor_bio' => 'Should not save.',
            ])
            ->assertForbidden();
    }

    public function test_admin_viewing_cfm_portal_does_not_see_edit_button(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            TaskScenarioSeeder::class,
            OnboardingStepSeeder::class,
            CfmTrainingModuleSeeder::class,
            CfmManagementSeeder::class,
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('cfm.portal'));

        $response->assertOk()->assertSee('Admin view', false);

        $profileSection = str($response->getContent())->after('>Profile</h3>')->before('Achievements')->toString();
        $this->assertStringNotContainsString('>Edit</button>', $profileSection);
    }

    public function test_member_cannot_open_cfm_portal(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            OnboardingStepSeeder::class,
        ]);

        $member = User::factory()->create();
        $member->assignRole('member');

        $this->actingAs($member)
            ->get(route('cfm.portal'))
            ->assertForbidden();
    }

    public function test_agency_owner_cannot_open_cfm_portal(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            OnboardingStepSeeder::class,
        ]);

        $owner = User::factory()->create();
        $owner->assignRole('agency-owner');

        $this->actingAs($owner)
            ->get(route('cfm.portal'))
            ->assertForbidden();
    }
}
