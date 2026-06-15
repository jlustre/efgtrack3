<?php

namespace App\Livewire\Prospects;

use App\Models\Prospect;
use App\Services\Prospects\ProspectFunnelService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ProspectTimeline extends Component
{
    public Prospect $prospect;

    public string $filter = 'all';

    /** @var list<array{type: string, label: string, body: string, actor: string, occurred_at: string}> */
    public array $timeline = [];

    public function mount(Prospect $prospect, ProspectFunnelService $funnels): void
    {
        $this->prospect = $prospect;
        $this->authorize('view', $prospect);
        $this->loadTimeline($funnels);
    }

    public function updatedFilter(ProspectFunnelService $funnels): void
    {
        $this->loadTimeline($funnels);
    }

    #[On('prospect-timeline-refresh')]
    public function refreshTimeline(ProspectFunnelService $funnels): void
    {
        $this->loadTimeline($funnels);
    }

    private function loadTimeline(ProspectFunnelService $funnels): void
    {
        $items = $funnels->timelineFor($this->prospect->fresh());

        if ($this->filter !== 'all') {
            $items = array_values(array_filter($items, fn (array $item): bool => $item['type'] === $this->filter));
        }

        $this->timeline = $items;
    }

    public function render(): View
    {
        return view('livewire.prospects.prospect-timeline');
    }
}
