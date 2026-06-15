<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RankSeeder::class,
            RolePermissionSeeder::class,
            LocationLookupSeeder::class,
            EmailTemplateSeeder::class,
            TeamSeeder::class,
            OnboardingStepSeeder::class,
            LicensingStepSeeder::class,
            FieldApprenticeshipProgramSeeder::class,
            CfmTrainingModuleSeeder::class,
            ProspectLookupSeeder::class,
            UsersSeeder::class,
            TaskScenarioSeeder::class,
            TaskManagementSeeder::class,
        ]);

        if (app()->environment('local')) {
            $this->call([
                DownlineManagementSeeder::class,
            ]);
        }

        $this->call([
            CfmManagementSeeder::class,
            ProspectDemoSeeder::class,
            
            CalendarModuleSeeder::class,
            UserCalendarPreferenceSeeder::class,
            BookingSchedulingSeeder::class,
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
    }
}
