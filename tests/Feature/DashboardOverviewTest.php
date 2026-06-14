<?php

namespace Tests\Feature;

use App\Models\Rank;
use App\Models\User;
use App\Services\DownlineHierarchyService;
use App\Support\LocationOptions;
use Database\Seeders\CountrySeeder;
use Database\Seeders\LicensingStepSeeder;
use Database\Seeders\OnboardingStepSeeder;
use Database\Seeders\ProfileCompletionFieldSeeder;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StateProvinceSeeder;
use Database\Seeders\TeamSeeder;
use Database\Seeders\TimezoneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardOverviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RankSeeder::class,
            RolePermissionSeeder::class,
            CountrySeeder::class,
            StateProvinceSeeder::class,
            TimezoneSeeder::class,
            ProfileCompletionFieldSeeder::class,
            TeamSeeder::class,
            OnboardingStepSeeder::class,
            LicensingStepSeeder::class,
        ]);
    }

    public function test_authenticated_member_sees_development_journey_sections(): void
    {
        $teamId = (int) DB::table('teams')->value('id');
        $rankId = Rank::where('code', 'FA')->value('id');
        $user = $this->createUser('member.journey@example.com', 'Journey Member', 'member', $teamId, $rankId);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Your Development Journey', false)
            ->assertSeeText('Onboarding & Orientation')
            ->assertSeeText('Licensing & Compliance')
            ->assertSee('Field Apprenticeship Program', false)
            ->assertSeeText('Training & Assessments')
            ->assertSeeText('Mentor & Team Communications')
            ->assertSeeText('Progress & Performance')
            ->assertSeeText('Career Development & Rank Advancement')
            ->assertSee('Team Profile Completion', false)
            ->assertSee('My Progress', false);
    }

    public function test_seeded_onboarding_steps_appear_in_onboarding_hub(): void
    {
        $teamId = (int) DB::table('teams')->value('id');
        $rankId = Rank::where('code', 'FA')->value('id');
        $user = $this->createUser('onboarding.hub@example.com', 'Onboarding Hub Member', 'member', $teamId, $rankId);

        $firstStepTitle = DB::table('onboarding_steps')
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->value('title');

        $this->assertNotNull($firstStepTitle);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee($firstStepTitle, false)
            ->assertSee('Not started', false);
    }

    public function test_dashboard_still_renders_team_stat_cards_with_view_buttons(): void
    {
        $teamId = (int) DB::table('teams')->value('id');
        $rankId = Rank::where('code', 'FA')->value('id');
        $user = $this->createUser('viewer@example.com', 'Viewer User', 'member', $teamId, $rankId);

        app(DownlineHierarchyService::class)->rebuild();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Team Profile Completion')
            ->assertSee('Team Onboarding')
            ->assertSee('Team Licensing')
            ->assertSee('Team FAP')
            ->assertSee('Team CFM Training')
            ->assertSee('My Profile Completion')
            ->assertSee('View', false)
            ->assertDontSee('Next Rank Requirements', false)
            ->assertDontSee('Weekly leadership call reminder', false);
    }

    private function createUser(string $email, string $name, string $role, int $teamId, ?int $rankId, ?int $sponsorId = null): User
    {
        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make('Password123'),
            'team_id' => $teamId,
            'rank_id' => $rankId,
            'sponsor_id' => $sponsorId,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $user->syncRoles([$role]);

        $user->profile()->create(array_merge(
            LocationOptions::profileLocationIds('Canada'),
            [
                'city' => 'Toronto',
                'efg_associate_id' => 'EFG-'.$user->id,
            ]
        ));

        return $user;
    }
}
