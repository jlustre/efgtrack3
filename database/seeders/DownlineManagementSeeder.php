<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\Rank;
use App\Models\Team;
use App\Models\User;
use App\Services\DownlineHierarchyService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DownlineManagementSeeder extends Seeder
{
    public function run(): void
    {
        $team = Team::firstOrCreate(
            ['name' => 'Elite Financial Growth Team'],
            ['description' => 'Default downline team for EFGTrack demo hierarchy.', 'is_active' => true]
        );

        $root = User::firstOrCreate(
            ['email' => 'downline-owner@efgtrack.com'],
            [
                'name' => 'Morgan Executive',
                'password' => Hash::make('Password123'),
                'rank_id' => Rank::where('code', 'ED')->value('id'),
                'team_id' => $team->id,
                'is_active' => true,
                'joined_at' => now()->subYears(3),
            ]
        );
        $root->assignRole('agency-owner');

        $leaders = [
            ['name' => 'Avery Stone', 'email' => 'avery.stone@efgtrack.com', 'rank' => 'SM', 'role' => 'team-leader'],
            ['name' => 'Bianca Reyes', 'email' => 'bianca.reyes@efgtrack.com', 'rank' => 'SFA', 'role' => 'certified-field-mentor'],
            ['name' => 'Caleb Morgan', 'email' => 'caleb.morgan@efgtrack.com', 'rank' => 'SM', 'role' => 'team-leader'],
            ['name' => 'Dana Chen', 'email' => 'dana.chen@efgtrack.com', 'rank' => 'SFA', 'role' => 'trainer'],
        ];

        $createdLeaders = collect($leaders)->map(function (array $leader) use ($root, $team): User {
            $user = User::updateOrCreate(
                ['email' => $leader['email']],
                [
                    'name' => $leader['name'],
                    'password' => Hash::make('Password123'),
                    'rank_id' => Rank::where('code', $leader['rank'])->value('id'),
                    'team_id' => $team->id,
                    'sponsor_id' => $root->id,
                    'is_active' => true,
                    'joined_at' => now()->subMonths(rand(14, 30)),
                    'last_login_at' => now()->subDays(rand(1, 12)),
                ]
            );
            $user->assignRole($leader['role']);

            return $user;
        });

        $names = [
            'Elliot Carter', 'Farah Singh', 'Gabriel Cruz', 'Harper Lee', 'Iris Patel',
            'Jonas Wright', 'Kara Brooks', 'Luis Rivera', 'Mina Santos', 'Noah Evans',
            'Olivia Grant', 'Priya Thomas', 'Quinn Walker', 'Rafael Young', 'Sofia Kim',
            'Theo Bennett', 'Uma Flores', 'Victor Hall', 'Wendy Scott', 'Xavier Price',
            'Yara Nelson', 'Zane Cooper', 'Amelia Foster', 'Brandon King', 'Celeste Ross',
            'Dominic Gray', 'Elena Ward', 'Felix Hughes', 'Gia Coleman', 'Hugo Bell',
        ];

        $mentor = $createdLeaders->firstWhere('email', 'bianca.reyes@efgtrack.com') ?? $createdLeaders->first();

        foreach ($names as $index => $name) {
            $leader = $createdLeaders[$index % $createdLeaders->count()];
            $rankCode = $index % 5 === 0 ? 'SFA' : 'FA';
            $user = User::updateOrCreate(
                ['email' => str($name)->lower()->replace(' ', '.').'@efgtrack.com'],
                [
                    'name' => $name,
                    'password' => Hash::make('Password123'),
                    'rank_id' => Rank::where('code', $rankCode)->value('id'),
                    'team_id' => $team->id,
                    'sponsor_id' => $leader->id,
                    'mentor_id' => $mentor->id,
                    'is_active' => $index % 9 !== 0,
                    'joined_at' => now()->subDays(20 + ($index * 5)),
                    'last_login_at' => now()->subDays($index % 15),
                ]
            );
            $user->assignRole('member');
        }

        $countries = [
            ['Canada', 'Vancouver', 'America/Vancouver'],
            ['United States', 'Phoenix', 'America/Phoenix'],
            ['Canada', 'Toronto', 'America/Toronto'],
            ['United States', 'Dallas', 'America/Chicago'],
        ];

        User::query()->orderBy('id')->get()->each(function (User $user, int $index) use ($countries): void {
            [$country, $city, $timezone] = $countries[$index % count($countries)];

            Profile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => '555-010'.($index % 10),
                    'city' => $city,
                    'country' => $country,
                    'timezone' => $timezone,
                    'license_number' => $index % 3 === 0 ? 'LIC-'.$user->id.'-EFG' : null,
                    'efg_associate_id' => 'EFG-'.$user->id,
                    'is_efg_active_associate' => true,
                    'recruited_at' => $user->joined_at,
                ]
            );
        });

        app(DownlineHierarchyService::class)->rebuild();
    }
}
