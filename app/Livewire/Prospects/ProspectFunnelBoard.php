<?php

namespace App\Livewire\Prospects;

use App\Models\Prospect;
use App\Models\ProspectFunnel;
use App\Services\Prospects\ProspectFunnelService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class ProspectFunnelBoard extends Component
{
    #[Url(as: 'funnel')]
    public string $funnelType = 'insurance';

    public function mount(): void
    {
        $this->authorize('viewAny', Prospect::class);

        if (! in_array($this->funnelType, ['insurance', 'recruiting'], true)) {
            $this->funnelType = 'insurance';
        }
    }

    public function updatedFunnelType(): void
    {
        if (! in_array($this->funnelType, ['insurance', 'recruiting'], true)) {
            $this->funnelType = 'insurance';
        }
    }

    #[On('prospect-board-refresh')]
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

        $funnels->moveStage($prospect, auth()->user(), $stageId, 'kanban');
    }

    public function render(ProspectFunnelService $funnels): View
    {
        $funnel = ProspectFunnel::query()
            ->whereNull('user_id')
            ->where('key', $this->funnelType)
            ->where('is_active', true)
            ->firstOrFail();

        $stages = $funnels->stagesForFunnel($funnel->id);

        $stageIds = $stages->pluck('pipeline_stage_id')->filter()->all();

        $prospects = Prospect::query()
            ->where('owner_id', auth()->id())
            ->where('status', 'active')
            ->where('is_archived', false)
            ->when(
                $this->funnelType === 'recruiting',
                fn ($query) => $query->where('funnel_type', 'recruiting'),
                fn ($query) => $query->whereIn('funnel_type', ['insurance', 'both']),
            )
            ->whereIn('pipeline_stage_id', $stageIds)
            ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END")
            ->orderBy('next_follow_up_at')
            ->get(['id', 'first_name', 'last_name', 'preferred_name', 'phone', 'interest_level', 'priority', 'pipeline_stage_id', 'next_follow_up_at']);

        /** @var Collection<int|string, Collection<int, Prospect>> $grouped */
        $grouped = $prospects->groupBy(fn (Prospect $prospect): int => (int) $prospect->pipeline_stage_id);

        return view('livewire.prospects.prospect-funnel-board', [
            'funnel' => $funnel,
            'stages' => $stages,
            'groupedProspects' => $grouped,
            'funnelTypes' => collect(config('prospects.funnel_types'))->only(['insurance', 'recruiting']),
        ]);
    }
}
