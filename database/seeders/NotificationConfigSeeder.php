<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationConfigSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code' => 'training',
                'name' => 'Training',
                'description' => 'Training module assignments, completions, and reminders.',
                'icon' => 'academic-cap',
                'color' => '#2563EB',
                'sort_order' => 10,
            ],
            [
                'code' => 'mentoring',
                'name' => 'Mentoring',
                'description' => 'Mentor assignments, sessions, and apprenticeship updates.',
                'icon' => 'user-group',
                'color' => '#0F766E',
                'sort_order' => 20,
            ],
            [
                'code' => 'licensing',
                'name' => 'Licensing',
                'description' => 'Licensing checklist milestones and approvals.',
                'icon' => 'document-check',
                'color' => '#B45309',
                'sort_order' => 30,
            ],
            [
                'code' => 'rank_advancement',
                'name' => 'Rank Advancement',
                'description' => 'Rank progression and advancement milestones.',
                'icon' => 'arrow-trending-up',
                'color' => '#7C3AED',
                'sort_order' => 40,
            ],
            [
                'code' => 'system',
                'name' => 'System',
                'description' => 'Portal announcements, account activity, and system alerts.',
                'icon' => 'bell-alert',
                'color' => '#475569',
                'sort_order' => 50,
            ],
        ];

        foreach ($types as $type) {
            DB::table('notification_types')->updateOrInsert(
                ['code' => $type['code']],
                array_merge($type, [
                    'is_active' => true,
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $typeIds = DB::table('notification_types')->pluck('id', 'code');

        $triggers = [
            [
                'notification_type_id' => $typeIds['training'],
                'code' => 'training_assigned',
                'name' => 'Training Assigned',
                'description' => 'Fires when a training module is assigned to a member.',
                'event_key' => 'training.assigned',
                'sort_order' => 10,
            ],
            [
                'notification_type_id' => $typeIds['training'],
                'code' => 'training_completed',
                'name' => 'Training Completed',
                'description' => 'Fires when a member completes a training module.',
                'event_key' => 'training.completed',
                'sort_order' => 20,
            ],
            [
                'notification_type_id' => $typeIds['mentoring'],
                'code' => 'mentor_assigned',
                'name' => 'Mentor Assigned',
                'description' => 'Fires when a CFM is assigned as mentor.',
                'event_key' => 'mentor.assigned',
                'sort_order' => 10,
            ],
            [
                'notification_type_id' => $typeIds['mentoring'],
                'code' => 'booking_confirmed',
                'name' => 'Booking Confirmed',
                'description' => 'Fires when a mentor session booking is confirmed.',
                'event_key' => 'booking.confirmed',
                'sort_order' => 20,
            ],
            [
                'notification_type_id' => $typeIds['licensing'],
                'code' => 'licensing_step_approved',
                'name' => 'Licensing Step Approved',
                'description' => 'Fires when a licensing checklist step is approved.',
                'event_key' => 'licensing.step_approved',
                'sort_order' => 10,
            ],
            [
                'notification_type_id' => $typeIds['rank_advancement'],
                'code' => 'rank_advanced',
                'name' => 'Rank Advanced',
                'description' => 'Fires when a member advances to a new rank.',
                'event_key' => 'rank.advanced',
                'sort_order' => 10,
            ],
            [
                'notification_type_id' => $typeIds['system'],
                'code' => 'announcement_published',
                'name' => 'Announcement Published',
                'description' => 'Fires when a new announcement is published.',
                'event_key' => 'announcement.published',
                'sort_order' => 10,
            ],
        ];

        foreach ($triggers as $trigger) {
            DB::table('notification_triggers')->updateOrInsert(
                ['code' => $trigger['code']],
                array_merge($trigger, [
                    'is_active' => true,
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $triggerIds = DB::table('notification_triggers')->pluck('id', 'code');

        $templates = [
            [
                'notification_trigger_id' => $triggerIds['training_assigned'],
                'name' => 'Default Training Assigned',
                'subject' => 'New training assigned',
                'body' => '{{ member_name }}, a new training module is ready: {{ module_title }}.',
                'channels' => json_encode(['in_app']),
                'placeholders' => json_encode(['member_name', 'module_title']),
                'is_default' => true,
            ],
            [
                'notification_trigger_id' => $triggerIds['training_completed'],
                'name' => 'Default Training Completed',
                'subject' => 'Training completed',
                'body' => '{{ member_name }} completed {{ module_title }}.',
                'channels' => json_encode(['in_app']),
                'placeholders' => json_encode(['member_name', 'module_title']),
                'is_default' => true,
            ],
            [
                'notification_trigger_id' => $triggerIds['mentor_assigned'],
                'name' => 'Default Mentor Assigned',
                'subject' => 'Mentor assigned',
                'body' => '{{ member_name }}, {{ mentor_name }} is now your Certified Field Mentor.',
                'channels' => json_encode(['in_app', 'email']),
                'placeholders' => json_encode(['member_name', 'mentor_name']),
                'is_default' => true,
            ],
            [
                'notification_trigger_id' => $triggerIds['booking_confirmed'],
                'name' => 'Default Booking Confirmed',
                'subject' => 'Mentor session confirmed',
                'body' => 'Your session with {{ mentor_name }} is confirmed for {{ session_time }}.',
                'channels' => json_encode(['in_app']),
                'placeholders' => json_encode(['mentor_name', 'session_time']),
                'is_default' => true,
            ],
            [
                'notification_trigger_id' => $triggerIds['licensing_step_approved'],
                'name' => 'Default Licensing Step Approved',
                'subject' => 'Licensing milestone approved',
                'body' => '{{ step_title }} was approved for {{ member_name }}.',
                'channels' => json_encode(['in_app']),
                'placeholders' => json_encode(['step_title', 'member_name']),
                'is_default' => true,
            ],
            [
                'notification_trigger_id' => $triggerIds['rank_advanced'],
                'name' => 'Default Rank Advanced',
                'subject' => 'Rank advancement',
                'body' => 'Congratulations {{ member_name }} on advancing to {{ rank_name }}.',
                'channels' => json_encode(['in_app']),
                'placeholders' => json_encode(['member_name', 'rank_name']),
                'is_default' => true,
            ],
            [
                'notification_trigger_id' => $triggerIds['announcement_published'],
                'name' => 'Default Announcement Published',
                'subject' => 'New announcement',
                'body' => '{{ announcement_title }} is now available in the portal.',
                'channels' => json_encode(['in_app']),
                'placeholders' => json_encode(['announcement_title']),
                'is_default' => true,
            ],
        ];

        foreach ($templates as $template) {
            DB::table('notification_templates')->updateOrInsert(
                [
                    'notification_trigger_id' => $template['notification_trigger_id'],
                    'name' => $template['name'],
                ],
                array_merge($template, [
                    'is_active' => true,
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
