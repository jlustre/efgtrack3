<?php

namespace App\Livewire\Prospects;

use App\Models\Prospect;
use App\Models\ProspectNote;
use App\Services\Prospects\ProspectFunnelService;
use App\Services\Prospects\ProspectTaskBridge;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class ProspectQuickLogModal extends Component
{
    public bool $show = false;

    public string $activeTab = 'activity';

    public ?string $prospectId = null;

    public ?string $prospectName = null;

    public string $activity_type = 'phone_call';

    public string $activity_occurred_at = '';

    public ?string $activity_outcome = null;

    public ?string $activity_notes = null;

    public ?string $activity_next_action = null;

    public ?string $activity_next_follow_up_at = null;

    public ?int $communication_type_id = null;

    public string $direction = 'outbound';

    public string $contacted_at = '';

    public ?string $communication_outcome = null;

    public ?string $communication_notes = null;

    public ?int $duration_minutes = null;

    public string $noteBody = '';

    public bool $noteIsPrivate = false;

    public string $status = 'active';

    public string $interest_level = 'warm';

    public string $priority = 'medium';

    public ?int $pipeline_stage_id = null;

    #[On('open-prospect-quick-log-modal')]
    public function open(string $prospectId, ?string $tab = null, ?string $activityType = null): void
    {
        $prospect = Prospect::query()->with('stage')->findOrFail($prospectId);
        $this->authorize('update', $prospect);

        $this->prospectId = $prospectId;
        $this->prospectName = $prospect->displayName();
        $this->activeTab = in_array($tab, ['activity', 'communication', 'note', 'status'], true) ? $tab : 'activity';

        $this->activity_type = ($activityType && array_key_exists($activityType, config('prospects.activity_types')))
            ? $activityType
            : 'phone_call';
        $this->activity_occurred_at = now()->format('Y-m-d\TH:i');
        $this->contacted_at = now()->format('Y-m-d\TH:i');
        $this->direction = 'outbound';

        $this->status = $prospect->status;
        $this->interest_level = $prospect->interest_level;
        $this->priority = $prospect->priority;
        $this->pipeline_stage_id = $prospect->pipeline_stage_id;

        $this->reset([
            'activity_outcome',
            'activity_notes',
            'activity_next_action',
            'activity_next_follow_up_at',
            'communication_type_id',
            'communication_outcome',
            'communication_notes',
            'duration_minutes',
            'noteBody',
        ]);
        $this->noteIsPrivate = false;

        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
        $this->prospectId = null;
        $this->prospectName = null;
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['activity', 'communication', 'note', 'status'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function setQuickActivity(string $type): void
    {
        if (! array_key_exists($type, config('prospects.activity_types'))) {
            return;
        }

        $this->activity_type = $type;
        $this->activeTab = 'activity';
    }

    public function saveActivity(ProspectFunnelService $funnels, ProspectTaskBridge $tasks): void
    {
        $prospect = $this->prospect();
        $this->authorize('update', $prospect);

        $validated = $this->validate([
            'activity_type' => ['required', 'string', 'max:60'],
            'activity_occurred_at' => ['required', 'date'],
            'activity_outcome' => ['nullable', 'string', 'max:80'],
            'activity_notes' => ['nullable', 'string', 'max:5000'],
            'activity_next_action' => ['nullable', 'string', 'max:5000'],
            'activity_next_follow_up_at' => ['nullable', 'date'],
            'pipeline_stage_id' => ['nullable', 'integer', 'exists:pipeline_stages,id'],
        ]);

        $activity = $funnels->logActivity($prospect, auth()->user(), [
            'activity_type' => $validated['activity_type'],
            'occurred_at' => $validated['activity_occurred_at'],
            'outcome' => $validated['activity_outcome'],
            'notes' => $validated['activity_notes'],
            'next_action' => $validated['activity_next_action'],
            'next_follow_up_at' => $validated['activity_next_follow_up_at'],
            'pipeline_stage_id' => $validated['pipeline_stage_id'] ?? null,
        ]);

        if (filled($validated['activity_next_action'])) {
            $tasks->createFromActivity($activity);
        }

        $this->afterSave('Activity logged.');
    }

    public function saveCommunication(ProspectFunnelService $funnels): void
    {
        $prospect = $this->prospect();
        $this->authorize('update', $prospect);

        $validated = $this->validate([
            'communication_type_id' => ['nullable', 'exists:communication_types,id'],
            'direction' => ['required', 'in:inbound,outbound'],
            'contacted_at' => ['required', 'date'],
            'communication_outcome' => ['nullable', 'string', 'max:80'],
            'communication_notes' => ['nullable', 'string', 'max:5000'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
        ]);

        $funnels->logCommunication($prospect, auth()->user(), [
            'communication_type_id' => $validated['communication_type_id'],
            'direction' => $validated['direction'],
            'contacted_at' => $validated['contacted_at'],
            'outcome' => $validated['communication_outcome'],
            'notes' => $validated['communication_notes'],
            'duration_minutes' => $validated['duration_minutes'],
        ]);

        $this->afterSave('Communication logged.');
    }

    public function saveNote(ProspectFunnelService $funnels): void
    {
        $prospect = $this->prospect();
        $this->authorize('create', [ProspectNote::class, $prospect]);

        $this->validate([
            'noteBody' => ['required', 'string', 'max:5000'],
        ]);

        $funnels->addNote($prospect, auth()->user(), $this->noteBody, $this->noteIsPrivate);

        $this->afterSave('Note saved.');
    }

    public function saveStatus(ProspectFunnelService $funnels): void
    {
        $prospect = $this->prospect();
        $this->authorize('update', $prospect);

        $validated = $this->validate([
            'status' => ['required', 'string', 'max:40'],
            'interest_level' => ['required', 'in:cold,warm,hot'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'pipeline_stage_id' => ['nullable', 'integer', 'exists:pipeline_stages,id'],
        ]);

        $data = [
            'status' => $validated['status'],
            'interest_level' => $validated['interest_level'],
            'priority' => $validated['priority'],
        ];

        if (filled($validated['pipeline_stage_id'])) {
            $data['pipeline_stage_id'] = $validated['pipeline_stage_id'];
        }

        $funnels->updateProspect($prospect, auth()->user(), $data, 'manual');

        $this->afterSave('Prospect updated.');
    }

    public function render(ProspectFunnelService $funnels): View
    {
        $stages = collect();

        if ($this->prospectId) {
            $prospect = Prospect::query()->find($this->prospectId);
            if ($prospect) {
                $stages = collect($funnels->numberedStagesForFunnel($prospect->prospect_funnel_id));
            }
        }

        return view('livewire.prospects.prospect-quick-log-modal', [
            'activityTypes' => config('prospects.activity_types'),
            'quickActivityTypes' => [
                'phone_call' => 'Phone',
                'email' => 'Email',
                'text_message' => 'Text',
                'presentation' => 'Presentation',
                'follow_up' => 'Notification',
            ],
            'communicationTypes' => DB::table('communication_types')->where('is_active', true)->orderBy('sort_order')->get(),
            'statusOptions' => ['active', 'inactive', 'archived', 'converted', 'lost'],
            'stages' => $stages,
        ]);
    }

    private function prospect(): Prospect
    {
        return Prospect::query()->findOrFail($this->prospectId);
    }

    private function afterSave(string $message): void
    {
        session()->flash('prospect_quick_log_status', $message);
        $this->dispatch('prospect-timeline-refresh');
        $this->dispatch('prospect-board-refresh');
        $this->close();
    }
}
