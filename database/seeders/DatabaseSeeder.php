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
<<<<<<< HEAD
        $this->call([
            RankSeeder::class,
            RolePermissionSeeder::class,
            LocationLookupSeeder::class,
            EmailTemplateSeeder::class,
            CountrySeeder::class,
            StateProvinceSeeder::class,
            TimezoneSeeder::class,
            TeamSeeder::class,
            OnboardingStepSeeder::class,
            LicensingStepSeeder::class,
            FieldApprenticeshipProgramSeeder::class,
            CfmTrainingModuleSeeder::class,
            CfmTraineeChecklistItemSeeder::class,
            ProspectLookupSeeder::class,
            ProspectFunnelSeeder::class,
            UsersSeeder::class,
            TaskScenarioSeeder::class,
            TaskManagementSeeder::class,
        ]);
=======
        $this->call($this->requiredSeeders);
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0

        if ($this->shouldSeedTransactionalData() && $this->transactionalSeeders !== []) {
            // $this->call($this->transactionalSeeders);
        }
    }

<<<<<<< HEAD
        $this->call([
            CfmManagementSeeder::class,
            ProspectDemoSeeder::class,
            ProspectModuleTestSeeder::class,
            NotificationDemoSeeder::class,
            CalendarModuleSeeder::class,
            FnaLookupSeeder::class,
            FnaDemoSeeder::class,
            UserCalendarPreferenceSeeder::class,
            BookingSchedulingSeeder::class,
            ResourceDocumentSeeder::class,
            ResourceLinkSeeder::class,
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@efgtrack.com'],
            [
                'name' => 'EFGTrack Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
                'joined_at' => now(),
                'is_online' => false,
            ]
        );

        $admin->assignRole('super-admin');
=======
    protected function shouldSeedTransactionalData(): bool
    {
        return ! app()->isProduction();
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
    }
}
