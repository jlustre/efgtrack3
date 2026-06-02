<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Rank;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $createdUsers = [];

        // Get FA rank and member role
        $faRank = Rank::where('code', 'FA')->first();
        $memberRole = Role::where('name', 'member')->first();
        $superAdminRole = Role::where('name', 'super-admin')->first();

        // Create super-admin user
        $superAdmin = User::create([
            'name' => 'Joey Lustre',
            'email' => 'super-admin@efgtrack.com',
            'password' => Hash::make('Password123'),
            'joined_at' => now(),
            'is_online' => false,
        ]);
        $superAdmin->assignRole($superAdminRole);
        $createdUsers[] = $superAdmin;

        // Add 20 regular users with member role, FA rank, team_id 1, and sponsor info
        for ($i = 0; $i < 20; $i++) {
            $sponsor = $faker->randomElement($createdUsers);
            $user = User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('Password123'),
                'sponsor_id' => $sponsor->id,
                'rank_id' => $faRank ? $faRank->id : null,
                'team_id' => 1,
                'joined_at' => now(),
                'is_online' => false,
            ]);
            $user->assignRole($memberRole);
            $createdUsers[] = $user;
        }
    }
}
