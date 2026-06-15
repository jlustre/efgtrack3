<?php

namespace App\Livewire\Prospects;

use App\Models\Prospect;
use App\Services\Prospects\ProspectFunnelService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProspectEdit extends Component
{
    public Prospect $prospect;

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

    public string $status = 'active';

    public string $interest_level = 'warm';

    public ?int $interest_score = null;

    public string $priority = 'medium';

    public ?string $fna_status = null;

    public ?string $referral_source_name = null;

    public ?string $campaign_name = null;

    public ?string $next_follow_up_at = null;

    public ?string $notes_summary = null;

    public function mount(Prospect $prospect, ProspectFunnelService $funnels): void
    {
        $this->prospect = $prospect->load(['source', 'stage', 'funnel']);
        $this->authorize('update', $prospect);

        $prospectFunnelId = $prospect->prospect_funnel_id
            ?? $funnels->resolveFunnel($prospect->funnel_type ?? 'insurance')->id;

        $this->fill([
            'first_name' => $prospect->first_name ?? '',
            'last_name' => $prospect->last_name ?? '',
            'preferred_name' => $prospect->preferred_name,
            'email' => $prospect->email,
            'phone' => $prospect->phone,
            'home_phone' => $prospect->home_phone,
            'work_phone' => $prospect->work_phone,
            'address_line_1' => $prospect->address_line_1,
            'city' => $prospect->city,
            'state_province' => $prospect->state_province,
            'country' => $prospect->country,
            'postal_code' => $prospect->postal_code,
            'funnel_type' => $prospect->funnel_type ?? 'insurance',
            'prospect_funnel_id' => $prospectFunnelId,
            'prospect_source_id' => $prospect->prospect_source_id,
            'pipeline_stage_id' => $prospect->pipeline_stage_id,
            'status' => $prospect->status ?? 'active',
            'interest_level' => $prospect->interest_level ?? 'warm',
            'interest_score' => $prospect->interest_score,
            'priority' => $prospect->priority ?? 'medium',
            'fna_status' => $prospect->fna_status,
            'referral_source_name' => $prospect->referral_source_name,
            'campaign_name' => $prospect->campaign_name,
            'next_follow_up_at' => $prospect->next_follow_up_at?->format('Y-m-d\TH:i'),
            'notes_summary' => $prospect->notes_summary,
        ]);
    }

    public function updatedFunnelType(string $value, ProspectFunnelService $funnels): void
    {
        $funnel = $funnels->resolveFunnel($value);
        $this->prospect_funnel_id = $funnel->id;
    }

    public function save(ProspectFunnelService $funnels): void
    {
        $this->authorize('update', $this->prospect);

        $validated = $this->validate($this->rules());

        $funnels->updateProspect($this->prospect, auth()->user(), $validated);

        session()->flash('status', 'Prospect updated successfully.');

        $this->redirectRoute('team.prospects.records.show', $this->prospect, navigate: true);
    }

    public function render(ProspectFunnelService $funnels): View
    {
        return view('livewire.prospects.prospect-edit', [
            'funnels' => $funnels->funnelsForSelect(),
            'sources' => DB::table('prospect_sources')->where('is_active', true)->orderBy('sort_order')->get(),
            'stages' => $funnels->stagesForFunnel($this->prospect_funnel_id),
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
            'status' => ['required', 'string', 'max:60'],
            'interest_level' => ['required', 'in:cold,warm,hot'],
            'interest_score' => ['nullable', 'integer', 'min:1', 'max:10'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'fna_status' => ['nullable', 'string', 'max:40'],
            'referral_source_name' => ['nullable', 'string', 'max:255'],
            'campaign_name' => ['nullable', 'string', 'max:255'],
            'next_follow_up_at' => ['nullable', 'date'],
            'notes_summary' => ['nullable', 'string'],
        ];
    }
}
