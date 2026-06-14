<?php



namespace App\Livewire\Prospects;



use App\Models\Prospect;

use App\Services\Prospects\ProspectFunnelService;

use App\Services\Prospects\ProspectTaskBridge;

use Illuminate\Contracts\View\View;

use Livewire\Attributes\On;

use Livewire\Component;



class LogActivityModal extends Component

{

    public bool $show = false;



    public ?string $prospectId = null;



    public string $activity_type = 'phone_call';



    public string $activity_occurred_at = '';



    public ?string $activity_outcome = null;



    public ?string $activity_notes = null;



    public ?string $activity_next_action = null;



    public ?string $activity_next_follow_up_at = null;



    #[On('open-log-activity-modal')]

    public function open(string $prospectId, ?string $activityType = null): void

    {

        $prospect = Prospect::query()->findOrFail($prospectId);

        $this->authorize('update', $prospect);



        $this->prospectId = $prospectId;

        $this->activity_type = $activityType ?? 'phone_call';

        $this->activity_occurred_at = now()->format('Y-m-d\TH:i');

        $this->reset(['activity_outcome', 'activity_notes', 'activity_next_action', 'activity_next_follow_up_at']);

        $this->show = true;

    }



    public function close(): void

    {

        $this->show = false;

        $this->prospectId = null;

    }



    public function save(ProspectFunnelService $funnels, ProspectTaskBridge $tasks): void

    {

        $prospect = Prospect::query()->findOrFail($this->prospectId);

        $this->authorize('update', $prospect);



        $validated = $this->validate([

            'activity_type' => ['required', 'string', 'max:60'],

            'activity_occurred_at' => ['required', 'date'],

            'activity_outcome' => ['nullable', 'string', 'max:80'],

            'activity_notes' => ['nullable', 'string', 'max:5000'],

            'activity_next_action' => ['nullable', 'string', 'max:5000'],

            'activity_next_follow_up_at' => ['nullable', 'date'],

        ]);



        $activity = $funnels->logActivity($prospect, auth()->user(), [

            'activity_type' => $validated['activity_type'],

            'occurred_at' => $validated['activity_occurred_at'],

            'outcome' => $validated['activity_outcome'],

            'notes' => $validated['activity_notes'],

            'next_action' => $validated['activity_next_action'],

            'next_follow_up_at' => $validated['activity_next_follow_up_at'],

        ]);



        if (filled($validated['activity_next_action'])) {

            $tasks->createFromActivity($activity);

        }



        $this->close();

        $this->dispatch('prospect-timeline-refresh');

        $this->dispatch('prospect-board-refresh');

    }



    public function render(): View

    {

        return view('livewire.prospects.log-activity-modal', [

            'activityTypes' => config('prospects.activity_types'),

        ]);

    }

}

