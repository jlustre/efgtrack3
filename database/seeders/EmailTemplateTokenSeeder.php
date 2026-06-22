<?php

namespace Database\Seeders;

use App\Models\EmailTemplateToken;
use Illuminate\Database\Seeder;

class EmailTemplateTokenSeeder extends Seeder
{
    public function run(): void
    {
        $tokens = [
            [
                'key' => 'app_name',
                'name' => 'App Name',
                'description' => 'The application name from system configuration.',
                'sample_value' => 'EFGTrack',
                'sort_order' => 10,
            ],
            [
                'key' => 'base_url',
                'name' => 'Base URL',
                'description' => 'Application root URL without a trailing slash (from APP_URL).',
                'sample_value' => 'https://efgtrack.com',
                'sort_order' => 12,
            ],
            [
                'key' => 'path',
                'name' => 'Path',
                'description' => 'Path (and query string, when present) for the primary link in this email. Combine with {{ base_url }} to build a full URL.',
                'sample_value' => '/dashboard',
                'sort_order' => 14,
            ],
            [
                'key' => 'member_name',
                'name' => 'Member Name',
                'description' => 'Full name of the associate or trainee referenced in the email.',
                'sample_value' => 'Jane Smith',
                'sort_order' => 20,
            ],
            [
                'key' => 'member_email',
                'name' => 'Member Email',
                'description' => 'Email address of the associate or trainee.',
                'sample_value' => 'jane.smith@example.com',
                'sort_order' => 30,
            ],
            [
                'key' => 'member_id',
                'name' => 'Member ID',
                'description' => 'Internal user ID of the associate or trainee referenced in the email.',
                'sample_value' => '42',
                'sort_order' => 32,
            ],
            [
                'key' => 'member_phone',
                'name' => 'Member Phone',
                'description' => 'Phone number from the member profile.',
                'sample_value' => '(555) 123-4567',
                'sort_order' => 34,
            ],
            [
                'key' => 'sponsor_name',
                'name' => 'Sponsor Name',
                'description' => 'Name of the member who sponsored the associate.',
                'sample_value' => 'Alex Johnson',
                'sort_order' => 40,
            ],
            [
                'key' => 'agency_owner_name',
                'name' => 'Agency Owner Name',
                'description' => 'Name of the agency owner for the associate\'s team.',
                'sample_value' => 'Morgan Lee',
                'sort_order' => 50,
            ],
            [
                'key' => 'cfm_name',
                'name' => 'CFM Name',
                'description' => 'Name of the assigned Certified Field Mentor.',
                'sample_value' => 'Chris Rivera',
                'sort_order' => 60,
            ],
            [
                'key' => 'cfm_email',
                'name' => 'CFM Email',
                'description' => 'Email address of the assigned Certified Field Mentor.',
                'sample_value' => 'chris.rivera@example.com',
                'sort_order' => 70,
            ],
            [
                'key' => 'assigned_by_name',
                'name' => 'Assigned By',
                'description' => 'Name of the leader who submitted the CFM assignment.',
                'sample_value' => 'Agency leadership',
                'sort_order' => 80,
            ],
            [
                'key' => 'confirmation_url',
                'name' => 'Confirmation URL',
                'description' => 'Signed link for a CFM to confirm a trainee assignment.',
                'sample_value' => 'https://efgtrack.com/cfm/assignments/confirm/...',
                'sort_order' => 90,
            ],
            [
                'key' => 'dashboard_url',
                'name' => 'Dashboard URL',
                'description' => 'Link to the member or leader dashboard.',
                'sample_value' => 'https://efgtrack.com/dashboard',
                'sort_order' => 100,
            ],
            [
                'key' => 'profile_url',
                'name' => 'Profile URL',
                'description' => 'Link to the member profile page.',
                'sample_value' => 'https://efgtrack.com/profile',
                'sort_order' => 110,
            ],
            [
                'key' => 'registration_link',
                'name' => 'Registration Link',
                'description' => 'Invitation URL for a new recruit to complete registration.',
                'sample_value' => 'https://efgtrack.com/register/ABC123',
                'sort_order' => 120,
            ],
            [
                'key' => 'registration_code',
                'name' => 'Registration Code',
                'description' => 'Invitation code required during registration.',
                'sample_value' => 'ABC123EFG',
                'sort_order' => 130,
            ],
            [
                'key' => 'expires_at',
                'name' => 'Expires At',
                'description' => 'Formatted expiration date for an invitation.',
                'sample_value' => 'December 31, 2026',
                'sort_order' => 140,
            ],
            [
                'key' => 'cfm_portal_url',
                'name' => 'CFM Portal URL',
                'description' => 'Link to the Certified Field Mentor portal.',
                'sample_value' => 'https://efgtrack.com/cfm/portal',
                'sort_order' => 150,
            ],
            [
                'key' => 'first_contact_url',
                'name' => 'First Contact URL',
                'description' => 'Link used when a CFM sends the trainee first-contact workflow.',
                'sample_value' => 'https://efgtrack.com/cfm/portal',
                'sort_order' => 160,
            ],
        ];

        foreach ($tokens as $token) {
            EmailTemplateToken::updateOrCreate(
                ['key' => $token['key']],
                [
                    'name' => $token['name'],
                    'description' => $token['description'],
                    'sample_value' => $token['sample_value'],
                    'sort_order' => $token['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
