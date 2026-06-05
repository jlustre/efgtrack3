<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Reference data and configuration required for every environment.
     *
     * @var list<class-string<Seeder>>
     */
    protected array $requiredSeeders = [
        RankSeeder::class,
        RolePermissionSeeder::class,
        EmailTemplateSeeder::class,
        CountrySeeder::class,
        StateProvinceSeeder::class,
        TimezoneSeeder::class,
        TeamSeeder::class,
        OnboardingStepSeeder::class,
        LicensingStepSeeder::class,
        FieldApprenticeshipProgramSeeder::class,
        CfmTrainingModuleSeeder::class,
        ProspectLookupSeeder::class,
        UsersSeeder::class,
        ProfileCompletionFieldSeeder::class,
        NotificationConfigSeeder::class,
        FacilitySeeder::class,
    ];

    /**
     * Demo users, sample records, and module fixtures for local/staging only.
     *
     * @var list<class-string<Seeder>>
     */
    protected array $transactionalSeeders = [
        // TaskScenarioSeeder::class,
        // TaskManagementSeeder::class,
        // DownlineManagementSeeder::class,
        // CfmManagementSeeder::class,
        // ProspectDemoSeeder::class,
        // CalendarModuleSeeder::class,
        // UserCalendarPreferenceSeeder::class,
        // BookingSchedulingSeeder::class,
        // NotificationDemoSeeder::class,
    ];

    public function run(): void
    {
        $this->call($this->requiredSeeders);

        if ($this->shouldSeedTransactionalData() && $this->transactionalSeeders !== []) {
            // $this->call($this->transactionalSeeders);
        }
    }

    protected function shouldSeedTransactionalData(): bool
    {
        return ! app()->isProduction();
    }
}
