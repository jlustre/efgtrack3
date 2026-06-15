<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileCompletionFieldSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            ['field_key' => 'phone', 'label' => 'Phone', 'sort_order' => 10],
            ['field_key' => 'city', 'label' => 'City', 'sort_order' => 20],
            ['field_key' => 'country_id', 'label' => 'Country', 'sort_order' => 30],
            ['field_key' => 'state_province_id', 'label' => 'Province / State', 'sort_order' => 40],
            ['field_key' => 'timezone_id', 'label' => 'Timezone', 'sort_order' => 50],
            ['field_key' => 'best_contact_time', 'label' => 'Best Contact Time', 'sort_order' => 60],
            ['field_key' => 'license_number', 'label' => 'License Number', 'sort_order' => 70],
            ['field_key' => 'efg_associate_id', 'label' => 'EFG Associate ID', 'sort_order' => 80],
            ['field_key' => 'bio', 'label' => 'Bio', 'sort_order' => 90],
        ];

        foreach ($fields as $field) {
            DB::table('profile_completion_fields')->updateOrInsert(
                ['field_key' => $field['field_key']],
                [
                    'label' => $field['label'],
                    'sort_order' => $field['sort_order'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
};
