<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('teams')->updateOrInsert(
<<<<<<< HEAD
            ['name' => 'Wealth Legacy Alliance'],
=======
            ['name' => 'EFG Team'],
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
            [
                'description' => 'Default team for all users.',
                'leader_id' => null,
                'owner_id' => null,
                'is_active' => true,
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
