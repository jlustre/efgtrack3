<?php

namespace Tests\Feature;

use App\Events\ApplicantHired;
use App\Exceptions\ApplicantAlreadyHiredException;
use App\Models\Profile;
use App\Models\User;
use App\Services\ApplicantHireService;
use Database\Seeders\CountrySeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StateProvinceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApplicantHireTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolePermissionSeeder::class,
            CountrySeeder::class,
            StateProvinceSeeder::class,
        ]);
    }

    public function test_hire_sets_recruited_at_on_profile(): void
    {
        Event::fake([ApplicantHired::class]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create(['name' => 'Jordan Applicant']);
        Profile::query()->create([
            'user_id' => $user->id,
            'phone' => '555-0100',
            'city' => 'Dallas',
            'license_number' => 'LIC-123',
        ]);

        $hiredUser = app(ApplicantHireService::class)->hire($user, $admin, now()->parse('2026-06-01'));

        $this->assertTrue($hiredUser->isEmployee());
        $this->assertSame('2026-06-01', $hiredUser->profile->recruited_at->toDateString());
        $this->assertFalse(Schema::hasTable('bp_employees'));

        Event::assertDispatched(ApplicantHired::class, function (ApplicantHired $event) use ($user, $admin): bool {
            return $event->user->is($user->fresh())
                && $event->hiredBy->is($admin);
        });
    }

    public function test_hire_is_idempotent_guarded(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        Profile::query()->create(['user_id' => $user->id]);

        app(ApplicantHireService::class)->hire($user, $admin);

        $this->expectException(ApplicantAlreadyHiredException::class);
        app(ApplicantHireService::class)->hire($user->fresh(), $admin);
    }

    public function test_admin_can_hire_applicant_from_user_management(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create(['name' => 'Portal Applicant']);
        Profile::query()->create(['user_id' => $user->id]);

        $this->actingAs($admin)
            ->post(route('admin.users.hire', $user), [
                'hire_date' => '2026-06-15',
            ])
            ->assertRedirect(route('admin.users.edit', $user))
            ->assertSessionHas('status', 'applicant-hired');

        $user->refresh()->load('profile');

        $this->assertTrue($user->isEmployee());
        $this->assertSame('2026-06-15', $user->profile->recruited_at->toDateString());
    }
}
