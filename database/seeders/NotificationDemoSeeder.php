<?php

namespace Database\Seeders;

<<<<<<< HEAD
use App\Models\User;
use App\Notifications\AssignCfmReminderNotification;
use App\Notifications\RecommendCfmReminderNotification;
=======
use App\Models\NotificationTemplate;
use App\Models\User;
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationDemoSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
        $agencyOwner = User::query()->where('email', 'agency-owner@efgtrack.com')->first();

        if (! $agencyOwner) {
            return;
        }

        $membersNeedingCfm = User::query()
            ->where('sponsor_id', $agencyOwner->id)
            ->whereNull('mentor_id')
            ->where('id', '!=', $agencyOwner->id)
            ->get();

        foreach ($membersNeedingCfm as $member) {
            $agencyOwner->notify(new AssignCfmReminderNotification($member));
        }

        $sponsor = User::query()->where('email', 'sponsor@efgtrack.com')->first();

        if ($sponsor && $agencyOwner->id !== $sponsor->id) {
            foreach ($membersNeedingCfm->take(2) as $member) {
                $sponsor->notify(new RecommendCfmReminderNotification($member, $agencyOwner));
            }
        }

        $this->seedPortalAlerts($agencyOwner, [
            [
                'title' => 'Licensing milestone approved',
                'message' => 'Leo Grant completed the provincial licensing checklist step.',
                'category' => 'Licensing',
                'action_url' => route('licensing.index', [], false),
                'read_at' => null,
                'minutes_ago' => 15,
            ],
            [
                'title' => 'Training module assigned',
                'message' => 'Field Apprenticeship orientation is ready for Maya Chen.',
                'category' => 'Training',
                'action_url' => route('training.index', [], false),
                'read_at' => null,
                'minutes_ago' => 45,
            ],
            [
                'title' => 'Team event this week',
                'message' => 'Wealth Legacy Alliance weekly huddle starts Thursday at 7:00 PM.',
                'category' => 'Events',
                'action_url' => route('events.index', [], false),
                'read_at' => null,
                'minutes_ago' => 90,
            ],
            [
                'title' => 'Rank advancement submitted',
                'message' => 'Marcus Rivera submitted rank advancement paperwork for review.',
                'category' => 'Rank Advancement',
                'action_url' => route('rank-advancement.index', [], false),
                'read_at' => now()->subDay(),
                'minutes_ago' => 180,
            ],
            [
                'title' => 'New agency announcement',
                'message' => 'Q2 recruiting incentives are now published for your team.',
                'category' => 'Announcements',
                'action_url' => route('announcements.index', [], false),
                'read_at' => now()->subDays(2),
                'minutes_ago' => 360,
            ],
            [
                'title' => 'Onboarding checklist reminder',
                'message' => 'Nina Santos still has two onboarding steps waiting for completion.',
                'category' => 'Onboarding',
                'action_url' => route('onboarding.index', [], false),
                'read_at' => null,
                'minutes_ago' => 720,
            ],
            [
                'title' => 'CFM training review due',
                'message' => 'Celeste Nvarro has a CFM training module awaiting your sign-off.',
                'category' => 'Training',
                'action_url' => route('cfm-training.index', [], false),
                'read_at' => null,
                'minutes_ago' => 960,
            ],
        ]);
    }

    /**
     * @param  list<array{title: string, message: string, category: string, action_url: string, read_at: mixed, minutes_ago: int}>  $alerts
     */
    private function seedPortalAlerts(User $user, array $alerts): void
    {
        foreach ($alerts as $alert) {
            $createdAt = now()->subMinutes($alert['minutes_ago']);

            DB::table('notifications')->insert([
                'id' => (string) Str::uuid(),
                'type' => 'database',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'title' => $alert['title'],
                    'message' => $alert['message'],
                    'category' => $alert['category'],
                    'action_url' => $alert['action_url'],
                ]),
                'read_at' => $alert['read_at'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
=======
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
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
    }
}
