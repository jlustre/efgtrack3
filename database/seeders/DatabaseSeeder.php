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
        RankRequirementSeeder::class,
        RolePermissionSeeder::class,
        EmailTemplateSeeder::class,
        EmailTemplateTokenSeeder::class,
        CountrySeeder::class,
        StateProvinceSeeder::class,
        TimezoneSeeder::class,
        TeamSeeder::class,
        ChecklistTypeSeeder::class,
        ChecklistInstructionSeeder::class,
        ChecklistSeeder::class,
        ProspectLookupSeeder::class,
        ProspectFunnelSeeder::class,
        UsersSeeder::class,
        ProfileCompletionFieldSeeder::class,
        GoalCategorySeeder::class,
        GoalTemplateSeeder::class,
        GoalBadgeSeeder::class,
        CfmEffectivenessSeeder::class,
        ComplianceLifecycleSeeder::class,
        SupportModuleSeeder::class,
        NotificationConfigSeeder::class,
        AnnouncementCategorySeeder::class,
        AnnouncementTemplateSeeder::class,
        RecognitionBadgeSeeder::class,
        ResourceDocumentSeeder::class,
        ResourceVideoSeeder::class,
        TrainingAcademySeeder::class,
    ];

    /**
     * Demo users, sample records, and module fixtures for local/staging only.
     *
     * @var list<class-string<Seeder>>
     */
    protected array $transactionalSeeders = [
        TaskScenarioSeeder::class,
        TaskManagementSeeder::class,
        DownlineManagementSeeder::class,
        CfmManagementSeeder::class,
        ProspectDemoSeeder::class,
        CalendarModuleSeeder::class,
        MessagingModuleSeeder::class,
        UserCalendarPreferenceSeeder::class,
        BookingSchedulingSeeder::class,
        NotificationsSeeder::class,
        CommunicationHubDemoSeeder::class,
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
