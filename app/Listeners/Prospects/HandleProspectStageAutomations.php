<?php

namespace App\Listeners\Prospects;

use App\Events\Prospects\ProspectStageChanged;
use App\Models\ProspectFollowUp;
use App\Models\ProspectFunnelStage;
use App\Services\Prospects\ProspectTaskBridge;

class HandleProspectStageAutomations
{
    public function __construct(private ProspectTaskBridge $tasks) {}

    public function handle(ProspectStageChanged $event): void
    {
        if ($event->fromStageId === null) {
            return;
        }

        $prospect = $event->prospect;
        $funnelStage = ProspectFunnelStage::query()
            ->with('pipelineStage')
            ->where('prospect_funnel_id', $prospect->prospect_funnel_id)
            ->where('pipeline_stage_id', $event->toStageId)
            ->first();

        $followUpConfig = $funnelStage?->auto_task_template['follow_up'] ?? null;

        if (! is_array($followUpConfig)) {
            $pipelineSlug = $prospect->stage?->slug ?? $funnelStage?->pipelineStage?->slug;
            $followUpConfig = $pipelineSlug ? config("prospects.stage_automations.{$pipelineSlug}") : null;
        }

        if (is_array($followUpConfig)) {
            $offsetDays = (int) ($followUpConfig['offset_days'] ?? 1);

            ProspectFollowUp::create([
                'prospect_id' => $prospect->id,
                'assigned_user_id' => $prospect->owner_id,
                'due_at' => now()->addDays($offsetDays),
                'followup_type' => $followUpConfig['followup_type'] ?? 'stage_automation',
                'priority' => $followUpConfig['priority'] ?? 'medium',
                'status' => 'pending',
                'notes' => $followUpConfig['notes'] ?? null,
            ]);
        }

        $taskTemplate = $funnelStage?->auto_task_template['task'] ?? null;

        if (is_array($taskTemplate)) {
            $this->tasks->createFromStageTemplate($prospect, $event->actor, $taskTemplate);
        }
    }
}
