<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\LocationOptions;
use Database\Seeders\CountrySeeder;
use Database\Seeders\OnboardingStepSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TimezoneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class TaskPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_tasks_page_shows_confirmation_assignment_and_email_tasks(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
<<<<<<< HEAD
=======
            TimezoneSeeder::class,
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
            OnboardingStepSeeder::class,
        ]);

        $agencyOwner = User::factory()->create();
        $agencyOwner->assignRole('agency-owner');

        $member = User::factory()->create([
            'sponsor_id' => $agencyOwner->id,
            'mentor_id' => null,
        ]);
        $member->assignRole('member');
<<<<<<< HEAD
        $member->profile()->create(LocationOptions::profileAttributesForStorage([
            'country' => 'Canada',
            'is_efg_active_associate' => true,
            'efg_associate_id' => 'EFG-TASK-1',
        ]));
=======
        $member->profile()->create(array_merge([
            'is_efg_active_associate' => true,
            'efg_associate_id' => 'EFG-TASK-1',
        ], LocationOptions::profileLocationIds('Canada')));
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0

        $stepId = DB::table('onboarding_steps')
            ->where('title', 'Complete Member Profile')
            ->value('id');

        DB::table('user_onboarding_progress')->insert([
            'user_id' => $member->id,
            'onboarding_step_id' => $stepId,
            'status' => 'pending_confirmation',
            'submitted_at' => now()->subDay(),
            'completed_at' => null,
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        DB::table('registration_invitations')->insert([
            'sponsor_id' => $agencyOwner->id,
            'code' => Str::upper(Str::random(12)),
            'email' => 'prospect@example.com',
            'role_name' => 'member',
            'max_uses' => 1,
            'uses_count' => 0,
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $this->actingAs($agencyOwner)
            ->get(route('tasks.index'))
            ->assertOk()
            ->assertSee('Organize team priorities')
            ->assertSee('Complete Member Profile')
            ->assertSee('Add confirmation notes')
            ->assertSee('Reject')
            ->assertSee('Confirm')
            ->assertSee('Assign a CFM to '.$member->name)
            ->assertSee('Send invitation email')
            ->assertSee('prospect@example.com');
    }

    public function test_member_tasks_page_is_accessible_when_no_tasks_are_open(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $member = User::factory()->create();
        $member->assignRole('member');

        $this->actingAs($member)
            ->get(route('tasks.index'))
            ->assertOk()
            ->assertSee('No open tasks');
    }
}
