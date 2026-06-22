<?php

namespace App\Livewire\Recruiting;

use App\Models\Prospect;
use App\Models\ProspectFunnel;
use App\Services\Prospects\ProspectFunnelService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class RecruitingFunnelBoard extends Component
{
    public function mount(): void
    {
        $this->authorize('viewAny', Prospect::class);
    }

    #[On('recruiting-board-refresh')]
    public function refreshBoard(): void
    {
        // Re-render loads fresh data.
    }

    public function moveProspect(string $prospectId, int $stageId, ProspectFunnelService $funnels): void
    {
        $prospect = Prospect::query()->findOrFail($prospectId);
        $this->authorize('update', $prospect);

        if ((int) $prospect->pipeline_stage_id === $stageId) {
            return;
        }

        $funnels->moveStage($prospect, auth()->user(), $stageId, 'recruiting_kanban');
        $this->dispatch('recruiting-board-refresh');
    }

    public function render(ProspectFunnelService $funnels): View
    {
        $funnel = ProspectFunnel::query()
            ->whereNull('user_id')
            ->where('key', config('recruiting-pipeline.funnel_key', 'recruiting'))
            ->where('is_active', true)
            ->firstOrFail();

        $stages = $funnels->stagesForFunnel($funnel->id);
        $stageIds = $stages->pluck('pipeline_stage_id')->filter()->all();

        $prospects = Prospect::query()
            ->where('owner_id', auth()->id())
            ->where('status', 'active')
            ->where('is_archived', false)
            ->where('funnel_type', 'recruiting')
            ->whereIn('pipeline_stage_id', $stageIds)
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->orderBy('next_follow_up_at')
            ->get(['id', 'first_name', 'last_name', 'preferred_name', 'phone', 'interest_level', 'priority', 'pipeline_stage_id', 'next_follow_up_at']);

        $groupedProspects = $prospects->groupBy(fn (Prospect $prospect): int => (int) $prospect->pipeline_stage_id);

        return view('livewire.recruiting.recruiting-funnel-board', [
            'funnel' => $funnel,
            'stages' => $stages,
            'groupedProspects' => $groupedProspects,
        ]);
    }
}
