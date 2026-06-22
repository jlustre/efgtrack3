<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecognitionBadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            ['name' => 'New Recruit', 'slug' => 'new-recruit', 'icon' => '🌱', 'color' => '#22C55E', 'description' => 'Welcomed a new recruit to the team.'],
            ['name' => 'New License', 'slug' => 'new-license', 'icon' => '📜', 'color' => '#2563EB', 'description' => 'Earned a new insurance license.'],
            ['name' => 'First Sale', 'slug' => 'first-sale', 'icon' => '🏆', 'color' => '#C8A24A', 'description' => 'Closed a first production sale.'],
            ['name' => 'Promotion', 'slug' => 'promotion', 'icon' => '⬆️', 'color' => '#7C3AED', 'description' => 'Advanced to a new rank or role.'],
            ['name' => 'FAP Graduate', 'slug' => 'fap-graduate', 'icon' => '🎓', 'color' => '#0F766E', 'description' => 'Completed the Field Apprenticeship Program.'],
            ['name' => 'Top Producer', 'slug' => 'top-producer', 'icon' => '⭐', 'color' => '#C8A24A', 'description' => 'Recognized as a top producer.'],
            ['name' => 'Top Recruiter', 'slug' => 'top-recruiter', 'icon' => '🤝', 'color' => '#0891B2', 'description' => 'Recognized as a top recruiter.'],
            ['name' => 'Leadership Milestone', 'slug' => 'leadership-milestone', 'icon' => '🏛️', 'color' => '#0B1F3A', 'description' => 'Achieved a leadership milestone.'],
        ];

        foreach ($badges as $badge) {
            DB::table('badges')->updateOrInsert(
                ['slug' => $badge['slug']],
                [
                    'name' => $badge['name'],
                    'description' => $badge['description'],
                    'icon' => $badge['icon'],
                    'category' => 'recognition',
                    'color' => $badge['color'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }
}
