<?php

namespace Database\Seeders;

use App\Support\SystemTaskAssignor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SystemTaskAssignorSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('users')->where('id', SystemTaskAssignor::USER_ID)->exists()) {
            return;
        }

        DB::table('users')->insert([
            'id' => SystemTaskAssignor::USER_ID,
            'name' => SystemTaskAssignor::NAME,
            'email' => SystemTaskAssignor::EMAIL,
            'password' => Hash::make(str()->random(64)),
            'is_active' => false,
            'is_online' => false,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
