<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationDemoSeeder extends Seeder
{
    public function run(): void
    {
        $member = User::where('email', 'member@efgtrack.com')->first()
            ?? User::query()->whereNull('deleted_at')->orderBy('id')->first();

        if (! $member) {
            $this->command?->warn('NotificationDemoSeeder skipped: no users available.');

            return;
        }

        $mentor = User::where('email', 'cfm@efgtrack.com')->first();
        $trainingTrigger = DB::table('notification_triggers')->where('code', 'training_assigned')->first();
        $mentorTrigger = DB::table('notification_triggers')->where('code', 'mentor_assigned')->first();
        $licensingTrigger = DB::table('notification_triggers')->where('code', 'licensing_step_approved')->first();

        if (! $trainingTrigger || ! $mentorTrigger || ! $licensingTrigger) {
            $this->command?->warn('NotificationDemoSeeder skipped: run NotificationConfigSeeder first.');

            return;
        }

        $this->seedNotification(
            member: $member,
            trigger: $trainingTrigger,
            senderType: 'system',
            senderUserId: null,
            tokens: [
                'member_name' => $member->name,
                'module_title' => 'Product Knowledge Foundations',
            ],
            actionLink: [
                'label' => 'Open training',
                'url' => '/training',
            ],
            readAt: null,
        );

        $this->seedNotification(
            member: $member,
            trigger: $mentorTrigger,
            senderType: 'user',
            senderUserId: $mentor?->id,
            tokens: [
                'member_name' => $member->name,
                'mentor_name' => $mentor?->name ?? 'Your CFM',
            ],
            actionLink: [
                'label' => 'View mentor profile',
                'url' => '/cfm-portal',
            ],
            readAt: null,
        );

        $this->seedNotification(
            member: $member,
            trigger: $licensingTrigger,
            senderType: 'system',
            senderUserId: null,
            tokens: [
                'member_name' => $member->name,
                'step_title' => 'Pass the provincial exam',
            ],
            actionLink: [
                'label' => 'Review licensing checklist',
                'url' => '/tracker/licensing',
            ],
            readAt: now(),
        );
    }

    private function seedNotification(
        User $member,
        object $trigger,
        string $senderType,
        ?int $senderUserId,
        array $tokens,
        array $actionLink,
        $readAt,
    ): void {
        $template = NotificationTemplate::query()
            ->where('notification_trigger_id', $trigger->id)
            ->where('is_default', true)
            ->first();

        if (! $template) {
            return;
        }

        $subject = $template->renderSubject($tokens);
        $body = $template->renderBody($tokens);

        $payload = [
            'notification_type_id' => $trigger->notification_type_id,
            'trigger_id' => $trigger->id,
            'sender_type' => $senderType,
            'sender_user_id' => $senderUserId,
            'recipients' => json_encode([
                'user_ids' => [$member->id],
            ]),
            'notification_template' => json_encode($template->snapshot()),
            'action_link' => json_encode($actionLink),
            'type' => 'database',
            'notifiable_type' => User::class,
            'notifiable_id' => $member->id,
            'data' => json_encode([
                'title' => $subject,
                'message' => $body,
                'category' => DB::table('notification_types')->where('id', $trigger->notification_type_id)->value('name'),
            ]),
            'read_at' => $readAt,
            'deleted_at' => null,
            'updated_at' => now()->subHours(2),
        ];

        $existing = DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $member->id)
            ->where('trigger_id', $trigger->id)
            ->first();

        if ($existing) {
            DB::table('notifications')->where('id', $existing->id)->update($payload);

            return;
        }

        DB::table('notifications')->insert(array_merge($payload, [
            'id' => (string) Str::uuid(),
            'created_at' => now()->subHours(2),
        ]));
    }
}
