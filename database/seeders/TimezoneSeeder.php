<?php

namespace Database\Seeders;

use App\Support\LocationOptions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimezoneSeeder extends Seeder
{
    public function run(): void
    {
        $countryIds = DB::table('countries')->pluck('id', 'name');
        $sort = 0;

        foreach (LocationOptions::timezones() as $code => $label) {
            $countryId = $this->guessCountryId($code, $countryIds);

            DB::table('timezones')->updateOrInsert(
                ['code' => $code],
                [
                    'country_id' => $countryId,
                    'name' => $label,
                    'sort_order' => $sort++,
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<string, int>  $countryIds
     */
    private function guessCountryId(string $code, $countryIds): ?int
    {
        if (str_starts_with($code, 'Canada')) {
            return $countryIds['Canada'] ?? null;
        }

        if (str_starts_with($code, 'Mexico')) {
            return $countryIds['Mexico'] ?? null;
        }

        if ($code === 'Philippines Time') {
            return $countryIds['Philippines'] ?? null;
        }

        if (in_array($code, ['PST', 'MST', 'CST', 'EST'], true)) {
            return $countryIds['United States'] ?? null;
        }

        return null;
    }
}
