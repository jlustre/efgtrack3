<?php

namespace App\Services\Prospects;

use App\Models\Prospect;
use App\Models\ProspectActivity;
use App\Models\ProspectAppointment;
use App\Models\User;
use App\Models\UserTask;

class ProspectTaskBridge
{
    /**
     * @param  array<string, mixed>  $template
     */
    public function createFromStageTemplate(Prospect $prospect, User $actor, array $template): ?UserTask
    {
        $title = $template['title'] ?? $template['task'] ?? null;

        if (! is_string($title) || trim($title) === '') {
            return null;
        }

        return $this->createTask(
            prospect: $prospect,
            actor: $actor,
            title: $title,
            description: $template['description'] ?? $template['notes'] ?? null,
            priority: $template['priority'] ?? 'medium',
            dueDate: isset($template['offset_days'])
                ? now()->addDays((int) $template['offset_days'])->toDateString()
                : ($template['due_date'] ?? now()->addDay()->toDateString()),
        );
    }

    public function createFromActivity(ProspectActivity $activity): ?UserTask
    {
        if (! filled($activity->next_action)) {
            return null;
        }

        $activity->loadMissing(['prospect', 'user']);

        return $this->createTask(
            prospect: $activity->prospect,
            actor: $activity->user ?? $activity->prospect->owner,
            title: 'Follow up: '.$activity->prospect->displayName(),
            description: $activity->next_action,
            priority: 'medium',
            dueDate: ($activity->next_follow_up_at ?? now()->addDay())->toDateString(),
        );
    }

    public function createFromMissedAppointment(ProspectAppointment $appt): ?UserTask
    {
        $appt->loadMissing(['prospect', 'owner', 'type']);

        return $this->createTask(
            prospect: $appt->prospect,
            actor: $appt->owner,
            title: 'Reschedule missed appointment: '.$appt->prospect->displayName(),
            description: trim(($appt->type?->name ? $appt->type->name.' — ' : '').($appt->purpose ?? 'Follow up after missed appointment.')),
            priority: 'high',
            dueDate: now()->addDay()->toDateString(),
        );
    }

    private function createTask(
        Prospect $prospect,
        User $actor,
        string $title,
        ?string $description,
        string $priority,
        string $dueDate,
    ): UserTask {
        return UserTask::create([
            'assigned_to_user_id' => $prospect->owner_id,
            'created_by_user_id' => $actor->id,
            'title' => $title,
            'description' => $description,
            'priority' => in_array($priority, ['low', 'medium', 'high', 'urgent'], true) ? $priority : 'medium',
            'status' => 'to_do',
            'category' => 'Prospect Follow-Up',
            'related_module' => 'prospects',
            'related_person' => $prospect->displayName(),
            'related_prospect_id' => $prospect->id,
            'due_date' => $dueDate,
        ]);
    }
}
