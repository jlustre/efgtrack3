<?php

namespace App\Livewire\Prospects;

use App\Models\Prospect;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Component;

class ProspectLogActivityPicker extends Component
{
    public bool $show = false;

    public ?string $selectedProspectId = null;

    public string $search = '';

    #[On('open-prospect-log-activity-picker')]
    public function open(): void
    {
        $this->authorize('viewAny', Prospect::class);

        $this->reset(['selectedProspectId', 'search']);
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
    }

    public function selectProspect(string $prospectId): void
    {
        $prospect = Prospect::query()->findOrFail($prospectId);
        $this->authorize('update', $prospect);

        $this->selectedProspectId = $prospectId;
        $this->continueToLog();
    }

    public function continueToLog(): void
    {
        $this->validate([
            'selectedProspectId' => ['required', 'string'],
        ]);

        $prospect = Prospect::query()->findOrFail($this->selectedProspectId);
        $this->authorize('update', $prospect);

        $this->dispatch(
            'open-prospect-quick-log-modal',
            prospectId: $this->selectedProspectId,
            tab: 'activity',
            activityType: 'phone_call',
        );

        $this->show = false;
        $this->reset(['selectedProspectId', 'search']);
    }

    public function render(): View
    {
        $user = auth()->user();
        $needle = trim($this->search);

        $prospects = Prospect::query()
            ->where('owner_id', $user->id)
            ->where('status', 'active')
            ->where('is_archived', false)
            ->when($needle !== '', function (Builder $query) use ($needle): void {
                $query->where(function (Builder $inner) use ($needle): void {
                    $inner->where('first_name', 'like', "%{$needle}%")
                        ->orWhere('last_name', 'like', "%{$needle}%")
                        ->orWhereRaw("concat(first_name, ' ', last_name) like ?", ["%{$needle}%"]);
                });
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->limit(25)
            ->get(['id', 'first_name', 'last_name']);

        return view('livewire.prospects.prospect-log-activity-picker', [
            'prospects' => $prospects,
        ]);
    }
}
