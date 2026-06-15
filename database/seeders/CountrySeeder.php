<?php

namespace Database\Seeders;

use App\Support\LocationOptions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $codes = LocationOptions::countryDisplayCodes();
        $sort = 0;

        foreach (LocationOptions::countries() as $country) {
            DB::table('countries')->updateOrInsert(
                ['name' => $country],
                [
                    'code' => $codes[$country] ?? strtoupper(substr($country, 0, 2)),
                    'sort_order' => $sort++,
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }
    }
}
