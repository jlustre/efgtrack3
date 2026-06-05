<?php

namespace Tests\Feature;

use App\Events\ApplicantHired;
use App\Exceptions\ApplicantAlreadyHiredException;
use App\Models\BpEmployee;
use App\Models\BpEmpAddress;
use App\Models\BpEmpCredential;
use App\Models\BpEmpPhone;
use App\Models\BpJobData;
use App\Models\Country;
use App\Models\PeEmpAddress;
use App\Models\PeEmpCredential;
use App\Models\PeEmployee;
use App\Models\PeEmpPhone;
use App\Models\PeJobData;
use App\Models\Profile;
use App\Models\Rank;
use App\Models\Team;
use App\Models\User;
use App\Services\ApplicantHireService;
use App\Services\PreEmploymentSyncService;
use Database\Seeders\CountrySeeder;
use Database\Seeders\RankSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\StateProvinceSeeder;
use Database\Seeders\TimezoneSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ApplicantHireTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            RolePermissionSeeder::class,
            RankSeeder::class,
            CountrySeeder::class,
            StateProvinceSeeder::class,
            TimezoneSeeder::class,
        ]);
    }

    public function test_pre_employment_sync_creates_related_records_from_profile_and_user(): void
    {
        $rank = Rank::where('code', 'FA')->firstOrFail();
        $team = Team::create(['name' => 'Hire Team']);
        $sponsor = User::factory()->create();
        $country = Country::where('name', 'United States')->firstOrFail();

        $user = User::factory()->create([
            'name' => 'Jane Applicant',
            'email' => 'jane.applicant@example.com',
            'rank_id' => $rank->id,
            'team_id' => $team->id,
            'sponsor_id' => $sponsor->id,
            'joined_at' => '2026-05-01',
        ]);

        Profile::create([
            'user_id' => $user->id,
            'phone' => '555-0100',
            'city' => 'Austin',
            'country_id' => $country->id,
            'license_number' => 'LIC-12345',
            'efg_associate_id' => 'EFG-1001',
            'is_efg_active_associate' => true,
        ]);

        $peEmployee = app(PreEmploymentSyncService::class)->sync($user);

        $this->assertDatabaseHas('pe_employees', [
            'user_id' => $user->id,
            'first_name' => 'Jane',
            'last_name' => 'Applicant',
            'email' => 'jane.applicant@example.com',
            'efg_associate_id' => 'EFG-1001',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('pe_emp_addresses', [
            'pe_employee_id' => $peEmployee->id,
            'city' => 'Austin',
            'country_id' => $country->id,
            'is_primary' => true,
        ]);

        $this->assertDatabaseHas('pe_emp_phones', [
            'pe_employee_id' => $peEmployee->id,
            'phone_number' => '555-0100',
            'is_primary' => true,
        ]);

        $this->assertDatabaseHas('pe_job_data', [
            'pe_employee_id' => $peEmployee->id,
            'rank_id' => $rank->id,
            'team_id' => $team->id,
            'sponsor_id' => $sponsor->id,
        ]);

        $this->assertDatabaseHas('pe_emp_credentials', [
            'pe_employee_id' => $peEmployee->id,
            'credential_type' => 'license',
            'credential_number' => 'LIC-12345',
        ]);
    }

    public function test_hire_copies_pre_employment_data_to_bp_tables_and_sets_recruited_at(): void
    {
        Event::fake([ApplicantHired::class]);

        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $rank = Rank::where('code', 'FA')->firstOrFail();
        $team = Team::create(['name' => 'Hire Team']);
        $country = Country::where('name', 'United States')->firstOrFail();

        $user = User::factory()->create([
            'name' => 'Alex Applicant',
            'email' => 'alex.applicant@example.com',
            'rank_id' => $rank->id,
            'team_id' => $team->id,
            'joined_at' => '2026-05-01',
        ]);

        Profile::create([
            'user_id' => $user->id,
            'phone' => '555-0200',
            'city' => 'Dallas',
            'country_id' => $country->id,
            'license_number' => 'LIC-67890',
            'efg_associate_id' => 'EFG-2002',
            'is_efg_active_associate' => true,
        ]);

        $peEmployee = app(PreEmploymentSyncService::class)->sync($user);

        $bpEmployee = app(ApplicantHireService::class)->hire($user, $admin, now()->parse('2026-06-01'));

        $user->refresh()->load('profile');

        $this->assertTrue($user->isEmployee());
        $this->assertSame('2026-06-01', $user->profile->recruited_at->toDateString());
        $this->assertSame('hired', PeEmployee::find($peEmployee->id)->status);

        $this->assertDatabaseHas('bp_employees', [
            'id' => $bpEmployee->id,
            'user_id' => $user->id,
            'pe_employee_id' => $peEmployee->id,
            'first_name' => 'Alex',
            'last_name' => 'Applicant',
            'efg_associate_id' => 'EFG-2002',
            'hired_by' => $admin->id,
            'status' => 'active',
        ]);
        $this->assertSame('2026-06-01', $bpEmployee->hire_date->toDateString());

        $this->assertSame(1, BpEmpAddress::where('bp_employee_id', $bpEmployee->id)->count());
        $this->assertSame(1, BpEmpPhone::where('bp_employee_id', $bpEmployee->id)->count());
        $this->assertSame(1, BpJobData::where('bp_employee_id', $bpEmployee->id)->count());
        $this->assertSame(1, BpEmpCredential::where('bp_employee_id', $bpEmployee->id)->count());

        $this->assertSame(
            PeEmpAddress::where('pe_employee_id', $peEmployee->id)->value('city'),
            BpEmpAddress::where('bp_employee_id', $bpEmployee->id)->value('city'),
        );

        Event::assertDispatched(ApplicantHired::class, function (ApplicantHired $event) use ($user, $bpEmployee, $admin): bool {
            return $event->user->is($user)
                && $event->bpEmployee->is($bpEmployee)
                && $event->hiredBy->is($admin);
        });
    }

    public function test_hire_is_idempotent_and_rejects_already_hired_applicants(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $user = User::factory()->create(['name' => 'Already Hired']);
        Profile::create([
            'user_id' => $user->id,
            'efg_associate_id' => 'EFG-3003',
        ]);

        app(PreEmploymentSyncService::class)->sync($user);
        app(ApplicantHireService::class)->hire($user, $admin);

        $this->expectException(ApplicantAlreadyHiredException::class);
        app(ApplicantHireService::class)->hire($user->fresh(), $admin);

        $this->assertSame(1, BpEmployee::where('user_id', $user->id)->count());
    }

    public function test_super_admin_can_hire_applicant_from_admin_user_page(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $user = User::factory()->create(['name' => 'Portal Applicant']);
        Profile::create([
            'user_id' => $user->id,
            'phone' => '555-0300',
            'city' => 'Houston',
            'efg_associate_id' => 'EFG-4004',
        ]);

        app(PreEmploymentSyncService::class)->sync($user);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.users.hire', $user), [
                'hire_date' => '2026-06-15',
            ]);

        $response
            ->assertRedirect(route('admin.users.edit', $user))
            ->assertSessionHas('status', 'applicant-hired');

        $user->refresh()->load('profile', 'bpEmployee');

        $this->assertTrue($user->isEmployee());
        $this->assertNotNull($user->bpEmployee);
        $this->assertSame('2026-06-15', $user->profile->recruited_at->toDateString());
        $this->assertSame('Portal', $user->bpEmployee->first_name);
    }
}
