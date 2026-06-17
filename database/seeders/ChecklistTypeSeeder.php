<?php

namespace Database\Seeders;

use App\Models\ChecklistType;
use Illuminate\Database\Seeder;

class ChecklistTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code' => 'onboarding',
                'name' => 'Onboarding',
                'description' => 'Steps new members complete during onboarding and profile setup.',
                'icon' => 'clipboard',
                'sort_order' => 10,
            ],
            [
                'code' => 'licensing',
                'name' => 'Licensing',
                'description' => 'Provincial or state licensing milestones and compliance requirements.',
                'icon' => 'badge',
                'sort_order' => 20,
            ],
            [
                'code' => 'fap',
                'name' => 'Field Apprenticeship Program',
                'description' => 'Field Apprenticeship Program milestones for associates and trainees.',
                'icon' => 'academic',
                'sort_order' => 30,
            ],
            [
                'code' => 'cfm-training',
                'name' => 'CFM Training',
                'description' => 'Certified Field Mentor certification training modules and requirements.',
                'icon' => 'book',
                'sort_order' => 40,
            ],
            [
                'code' => 'cfm-mentoring',
                'name' => 'CFM Mentoring',
                'description' => 'Mentor-led trainee checklist items tracked by assigned CFMs.',
                'icon' => 'mentor',
                'sort_order' => 50,
            ],
            [
                'code' => 'training',
                'name' => 'Training',
                'description' => 'General training modules, lessons, and completion tracking.',
                'icon' => 'layers',
                'sort_order' => 60,
            ],
            [
                'code' => 'rank-advancement',
                'name' => 'Rank Advancement',
                'description' => 'Rank promotion requirements and advancement milestones.',
                'icon' => 'rank',
                'sort_order' => 70,
            ],
        ];

        foreach ($types as $type) {
            ChecklistType::query()->updateOrCreate(
                ['code' => $type['code']],
                array_merge($type, ['is_active' => true]),
            );
        }
    }
}
