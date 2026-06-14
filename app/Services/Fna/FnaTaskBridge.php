<?php

namespace App\Services\Fna;

use App\Models\FnaRecord;
use App\Models\User;
use App\Models\UserTask;

class FnaTaskBridge
{
    /**
     * @param  array<string, mixed>  $template
     */
    public function createFromTemplate(FnaRecord $fna, User $actor, array $template, ?User $assignee = null): ?UserTask
    {
        $title = $template['title'] ?? null;

        if (! is_string($title) || trim($title) === '') {
            return null;
        }

        $replacements = [
            '{client}' => $fna->client_name,
            '{trainee}' => $fna->owner?->name ?? 'Trainee',
        ];

        $title = str_replace(array_keys($replacements), array_values($replacements), $title);

        $assigneeId = $assignee?->id
            ?? (($template['assignee'] ?? null) === 'cfm' ? $fna->cfm_user_id : $fna->owner_user_id);

        if (! $assigneeId) {
            return null;
        }

        $offsetDays = (int) ($template['offset_days'] ?? 1);

        return UserTask::create([
            'assigned_to_user_id' => $assigneeId,
            'created_by_user_id' => $actor->id,
            'title' => $title,
            'description' => $template['description'] ?? null,
            'priority' => $template['priority'] ?? 'medium',
            'status' => 'to_do',
            'category' => 'FNA',
            'related_module' => 'fna',
            'related_person' => $fna->client_name,
            'related_prospect_id' => $fna->prospect_id,
            'related_fna_id' => $fna->id,
            'due_date' => now()->addDays($offsetDays)->toDateString(),
        ]);
    }
}
