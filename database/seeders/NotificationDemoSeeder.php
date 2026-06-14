<?php

namespace Database\Seeders;

use App\Models\User;
use App\Notifications\AssignCfmReminderNotification;
use App\Notifications\RecommendCfmReminderNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationDemoSeeder extends Seeder
{
    public function run(): void
    {
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
    }
}
