<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Profile;
use App\Models\User;
use Database\Seeders\FacilitySeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilitiesWebsitesTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_view_facilities_page_with_seeded_data(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            FacilitySeeder::class,
        ]);

        $user = User::factory()->create();
        $user->assignRole('member');

        Profile::query()->create([
            'user_id' => $user->id,
            'recruited_at' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->get(route('facilities.index'))
            ->assertOk()
            ->assertSee('Facilities Websites', false)
            ->assertSee('EFG Toronto Centre', false)
            ->assertSee('Toronto, ON', false)
            ->assertSee('toronto.efgtrack.com', false)
            ->assertSee('Arielle Morgan', false)
            ->assertSee('Agency Owner', false)
            ->assertSee('View', false);
    }

    public function test_pre_employment_user_cannot_access_facilities_page(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            FacilitySeeder::class,
        ]);

        $user = User::factory()->create();
        $user->assignRole('member');

        Profile::query()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('facilities.index'))
            ->assertForbidden();
    }

    public function test_employee_sees_facilities_link_in_sidebar(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        Profile::query()->create([
            'user_id' => $user->id,
            'recruited_at' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Facilities Websites', false);
    }

    public function test_pre_employment_user_does_not_see_facilities_link_in_sidebar(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        Profile::query()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Facilities Websites', false);
    }

    public function test_facilities_link_highlights_on_facilities_page(): void
    {
        $this->seed([
            RolePermissionSeeder::class,
            FacilitySeeder::class,
        ]);

        $user = User::factory()->create();
        $user->assignRole('member');

        Profile::query()->create([
            'user_id' => $user->id,
            'recruited_at' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->get(route('facilities.index'))
            ->assertOk()
            ->assertSee('data-server-active-item="top-facilities-websites"', false);
    }

    public function test_inactive_facilities_are_hidden_from_index(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('member');

        Profile::query()->create([
            'user_id' => $user->id,
            'recruited_at' => now()->toDateString(),
        ]);

        Facility::query()->create([
            'name' => 'Active Facility',
            'location' => 'Ottawa, ON',
            'phone' => '(613) 555-0100',
            'domain' => 'active.efgtrack.com',
            'is_active' => true,
        ]);

        Facility::query()->create([
            'name' => 'Inactive Facility',
            'location' => 'Montreal, QC',
            'phone' => '(514) 555-0200',
            'domain' => 'inactive.efgtrack.com',
            'is_active' => false,
        ]);

        $this->actingAs($user)
            ->get(route('facilities.index'))
            ->assertOk()
            ->assertSee('Active Facility', false)
            ->assertDontSee('Inactive Facility', false);
    }
}
