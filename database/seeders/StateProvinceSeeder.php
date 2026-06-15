<?php

namespace Database\Seeders;

use App\Support\LocationOptions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $countryIds = DB::table('countries')->pluck('id', 'name');
        $provinceCodes = [
            'Canada' => LocationOptions::class.'::canadaProvinceCodes',
        ];

        foreach (LocationOptions::provincesByCountry() as $countryName => $provinces) {
            $countryId = $countryIds[$countryName] ?? null;

            if (! $countryId) {
                continue;
            }

            $sort = 0;

            foreach ($provinces as $provinceName => $label) {
                $code = LocationOptions::provinceDisplayCode($countryName, $provinceName);

                DB::table('state_provinces')->updateOrInsert(
                    ['country_id' => $countryId, 'name' => $provinceName],
                    [
                        'code' => $code,
                        'sort_order' => $sort++,
                        'is_active' => true,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ],
                );
            }
        }
    }
}
