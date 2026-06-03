<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Core login accounts only. Sponsorship genealogy lives in DownlineManagementSeeder.
 */
class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super-admin')->firstOrFail();

        $superAdmin = User::updateOrCreate(
            ['email' => 'super-admin@efgtrack.com'],
            [
                'name' => 'Joey Lustre',
                'password' => Hash::make('Password123'),
                'joined_at' => now(),
                'is_online' => false,
                'is_active' => true,
            ]
        );

        if (! $superAdmin->hasRole($superAdminRole->name)) {
            $superAdmin->assignRole($superAdminRole);
        }
    }
}
