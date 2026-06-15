<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = [
            [
                'name' => 'EFG Toronto Centre',
                'location' => 'Toronto, ON',
                'phone' => '(416) 555-0101',
                'domain' => 'toronto.efgtrack.com',
                'address_line_1' => '250 Bay Street',
                'address_line_2' => 'Suite 1400',
                'city' => 'Toronto',
                'state_province' => 'Ontario',
                'postal_code' => 'M5J 2N4',
                'country' => 'Canada',
                'description' => 'Primary training and licensing support centre for the Greater Toronto Area.',
                'leadership' => [
                    ['name' => 'Arielle Morgan', 'title' => 'Agency Owner', 'email' => 'arielle.morgan@efgtrack.com', 'phone' => '(416) 555-0110'],
                    ['name' => 'Celeste Navarro', 'title' => 'Field Training Director', 'email' => 'celeste.navarro@efgtrack.com', 'phone' => '(416) 555-0111'],
                    ['name' => 'Jordan Blake', 'title' => 'Office Manager', 'email' => 'jordan.blake@efgtrack.com'],
                ],
                'sort_order' => 10,
            ],
            [
                'name' => 'EFG Vancouver Hub',
                'location' => 'Vancouver, BC',
                'phone' => '(604) 555-0202',
                'domain' => 'vancouver.efgtrack.com',
                'address_line_1' => '1055 West Georgia Street',
                'city' => 'Vancouver',
                'state_province' => 'British Columbia',
                'postal_code' => 'V6E 3P3',
                'country' => 'Canada',
                'description' => 'West coast field apprenticeship coordination and mentor scheduling hub.',
                'leadership' => [
                    ['name' => 'Maya Chen', 'title' => 'Regional Director', 'email' => 'maya.chen@efgtrack.com', 'phone' => '(604) 555-0220'],
                    ['name' => 'Ethan Brooks', 'title' => 'CFM Program Lead', 'email' => 'ethan.brooks@efgtrack.com'],
                ],
                'sort_order' => 20,
            ],
            [
                'name' => 'EFG Calgary Office',
                'location' => 'Calgary, AB',
                'phone' => '(403) 555-0303',
                'domain' => 'calgary.efgtrack.com',
                'address_line_1' => '855 2 Street SW',
                'address_line_2' => 'Floor 12',
                'city' => 'Calgary',
                'state_province' => 'Alberta',
                'postal_code' => 'T2P 4K7',
                'country' => 'Canada',
                'description' => 'Prairie region licensing intake, onboarding events, and team support.',
                'leadership' => [
                    ['name' => 'Priya Desai', 'title' => 'Office Director', 'email' => 'priya.desai@efgtrack.com', 'phone' => '(403) 555-0330'],
                    ['name' => 'Liam O\'Connor', 'title' => 'Training Coordinator', 'email' => 'liam.oconnor@efgtrack.com'],
                    ['name' => 'Sofia Martinez', 'title' => 'Client Services Lead', 'email' => 'sofia.martinez@efgtrack.com'],
                ],
                'sort_order' => 30,
            ],
        ];

        foreach ($facilities as $facility) {
            Facility::query()->updateOrCreate(
                ['name' => $facility['name']],
                $facility + ['is_active' => true],
            );
        }
    }
}
