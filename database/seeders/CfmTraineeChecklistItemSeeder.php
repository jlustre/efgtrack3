<?php

namespace Database\Seeders;

use App\Models\CfmTraineeChecklistItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CfmTraineeChecklistItemSeeder extends Seeder
{
    public function run(): void
    {
        $phases = require __DIR__.'/data/cfm_trainee_checklist_phases.php';
        $sortOrder = 0;

        foreach ($phases as $phase) {
            foreach ($phase['sections'] as $sectionTitle => $items) {
                foreach ($items as $title) {
                    $sortOrder++;
                    $slug = Str::slug('phase_'.$phase['phase_number'].'_'.Str::slug($title));

                    CfmTraineeChecklistItem::query()->updateOrCreate(
                        ['slug' => $slug],
                        [
                            'phase_number' => $phase['phase_number'],
                            'phase_title' => $phase['phase_title'],
                            'phase_target' => $phase['phase_target'],
                            'section_title' => $sectionTitle,
                            'title' => $title,
                            'sort_order' => $sortOrder,
                            'is_required' => true,
                            'is_active' => true,
                        ],
                    );
                }
            }
        }
    }
}
