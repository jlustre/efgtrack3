<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        EmailTemplate::updateOrCreate(
            ['key' => 'member_invitation'],
            [
                'name' => 'Member Invitation',
                'subject' => 'You are invited to join {{ app_name }}',
                'body' => "Hi,\n\n{{ sponsor_name }} has invited you to join {{ app_name }}, our private Experior Financial Group team tracking portal.\n\nUse this secure registration link to create your account:\n{{ registration_link }}\n\nYour registration code is: {{ registration_code }}\n\nThis invitation is single-use and expires on {{ expires_at }}.\n\nWelcome,\n{{ sponsor_name }}",
                'is_active' => true,
            ]
        );
    }
}
