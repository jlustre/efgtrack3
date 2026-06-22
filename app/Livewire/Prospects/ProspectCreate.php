<?php

namespace App\Livewire\Prospects;

use App\Models\Prospect;
use App\Services\Prospects\ProspectFunnelService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProspectCreate extends Component
{
    public string $first_name = '';

    public string $last_name = '';

    public ?string $preferred_name = null;

    public ?string $email = null;

    public ?string $phone = null;

    public ?string $home_phone = null;

    public ?string $work_phone = null;

    public ?string $address_line_1 = null;

    public ?string $city = null;

    public ?string $state_province = null;

    public ?string $country = null;

    public ?string $postal_code = null;

    public string $funnel_type = 'insurance';

    public ?int $prospect_funnel_id = null;

    public ?int $prospect_source_id = null;

    public ?int $pipeline_stage_id = null;

    public string $interest_level = 'warm';

    public ?int $interest_score = null;

    public string $priority = 'medium';

    public ?string $fna_status = 'not_started';

    public ?string $referral_source_name = null;

    public ?string $campaign_name = null;

    public ?string $notes_summary = null;

    public function mount(ProspectFunnelService $funnels): void
    {
        $this->authorize('create', Prospect::class);

        if (request()->query('funnel_type') === 'recruiting') {
            $this->funnel_type = 'recruiting';
        }

        $defaultFunnel = $funnels->resolveFunnel($this->funnel_type);
        $this->prospect_funnel_id = $defaultFunnel->id;
        $this->pipeline_stage_id = $funnels->numberedStagesForFunnel($defaultFunnel->id)[0]['id'] ?? null;
    }

    public function updatedFunnelType(string $value, ProspectFunnelService $funnels): void
    {
        $funnel = $funnels->resolveFunnel($value);
        $this->prospect_funnel_id = $funnel->id;
        $this->pipeline_stage_id = $funnels->numberedStagesForFunnel($funnel->id)[0]['id'] ?? null;
    }

    public function save(ProspectFunnelService $funnels): void
    {
        $this->authorize('create', Prospect::class);

        $validated = $this->validate($this->rules());

        $prospect = $funnels->createProspect(auth()->user(), $validated);

        session()->flash('status', 'Prospect created successfully.');

        $this->redirectRoute('team.prospects.records.show', $prospect, navigate: true);
    }

    public function render(ProspectFunnelService $funnels): View
    {
        return view('livewire.prospects.prospect-create', [
            'funnels' => $funnels->funnelsForSelect(),
            'sources' => DB::table('prospect_sources')->where('is_active', true)->orderBy('sort_order')->get(),
            'stages' => $funnels->numberedStagesForFunnel($this->prospect_funnel_id),
            'funnelTypes' => config('prospects.funnel_types'),
            'fnaStatuses' => config('prospects.fna_statuses'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'preferred_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:60'],
            'home_phone' => ['nullable', 'string', 'max:60'],
            'work_phone' => ['nullable', 'string', 'max:60'],
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state_province' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:40'],
            'funnel_type' => ['required', 'in:insurance,recruiting,both'],
            'prospect_funnel_id' => ['required', 'exists:prospect_funnels,id'],
            'prospect_source_id' => ['nullable', 'exists:prospect_sources,id'],
            'pipeline_stage_id' => ['nullable', 'exists:pipeline_stages,id'],
            'interest_level' => ['required', 'in:cold,warm,hot'],
            'interest_score' => ['nullable', 'integer', 'min:1', 'max:10'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'fna_status' => ['nullable', 'string', 'max:40'],
            'referral_source_name' => ['nullable', 'string', 'max:255'],
            'campaign_name' => ['nullable', 'string', 'max:255'],
            'notes_summary' => ['nullable', 'string'],
        ];
    }
}
