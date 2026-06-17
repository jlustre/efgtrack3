<?php

namespace App\Services\Goals;

use App\Models\Goal;
use App\Models\GoalReminder;
use App\Models\User;
use App\Notifications\Goals\GoalOffTrackNotification;
use App\Notifications\Goals\GoalReminderNotification;
use Illuminate\Support\Facades\Notification;

class GoalReminderService
{
    public function processDueReminders(): void
    {
        GoalReminder::query()
            ->where('is_active', true)
            ->whereNull('sent_at')
            ->where('remind_at', '<=', now())
            ->with(['goal.coaches', 'user'])
            ->each(function (GoalReminder $reminder): void {
                $goal = $reminder->goal;

                if (! $goal) {
                    $reminder->update(['is_active' => false]);

                    return;
                }

                $reminder->user?->notify(new GoalReminderNotification($goal, $reminder->message));

                $coachIds = $goal->coaches()
                    ->where('receives_alerts', true)
                    ->pluck('coach_user_id');

                if ($coachIds->isNotEmpty()) {
                    $coaches = User::query()->whereIn('id', $coachIds)->get();
                    Notification::send($coaches, new GoalReminderNotification($goal, $reminder->message, forCoach: true));
                }

                $reminder->update(['sent_at' => now()]);

                if (str_contains($reminder->message, 'Weekly check-in')) {
                    GoalReminder::query()->create([
                        'goal_id' => $goal->id,
                        'user_id' => $goal->user_id,
                        'remind_at' => now()->addWeek(),
                        'channel' => $reminder->channel,
                        'message' => $reminder->message,
                        'is_active' => true,
                    ]);
                }
            });
    }

    public function notifyOffTrackGoals(User $user): void
    {
        Goal::query()
            ->where('user_id', $user->id)
            ->where('status', 'off_track')
            ->each(function (Goal $goal) use ($user): void {
                $user->notify(new GoalOffTrackNotification($goal));

                $coachIds = $goal->coaches()->where('receives_alerts', true)->pluck('coach_user_id');

                if ($coachIds->isNotEmpty()) {
                    $coaches = User::query()->whereIn('id', $coachIds)->get();
                    Notification::send($coaches, new GoalOffTrackNotification($goal, forCoach: true));
                }
            });
    }
}
