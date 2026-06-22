<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    <label class="block md:col-span-2 xl:col-span-3">
        <span class="text-sm font-semibold text-slate-700">Funnel Type</span>
        <select wire:model.live="funnel_type" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
            @foreach ($funnelTypes as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        @error('funnel_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </label>

    <label class="block">
        <span class="text-sm font-semibold text-slate-700">First Name</span>
        <input wire:model="first_name" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
        @error('first_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Last Name</span>
        <input wire:model="last_name" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Preferred Name</span>
        <input wire:model="preferred_name" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Email</span>
        <input wire:model="email" type="email" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Mobile</span>
        <input wire:model="phone" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Home Phone</span>
        <input wire:model="home_phone" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Work Phone</span>
        <input wire:model="work_phone" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block xl:col-span-2">
        <span class="text-sm font-semibold text-slate-700">Address</span>
        <input wire:model="address_line_1" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">City</span>
        <input wire:model="city" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">State / Province</span>
        <input wire:model="state_province" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Country</span>
        <input wire:model="country" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Postal Code</span>
        <input wire:model="postal_code" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Pipeline Stage</span>
        <select wire:model="pipeline_stage_id" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
            <option value="">Select stage</option>
            @foreach ($stages as $stage)
                <option value="{{ $stage['id'] }}">{{ $stage['label'] }}</option>
            @endforeach
        </select>
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Lead Source</span>
        <select wire:model="prospect_source_id" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
            <option value="">No source</option>
            @foreach ($sources as $source)
                <option value="{{ $source->id }}">{{ $source->name }}</option>
            @endforeach
        </select>
    </label>
    @if (! empty($includeStatus))
        <label class="block">
            <span class="text-sm font-semibold text-slate-700">Status</span>
            <select wire:model="status" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
                @foreach (['active', 'inactive', 'archived'] as $statusOption)
                    <option value="{{ $statusOption }}">{{ str($statusOption)->title() }}</option>
                @endforeach
            </select>
        </label>
    @endif
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Interest Level</span>
        <select wire:model="interest_level" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
            @foreach (['cold', 'warm', 'hot'] as $level)
                <option value="{{ $level }}">{{ str($level)->title() }}</option>
            @endforeach
        </select>
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Interest Score (1-10)</span>
        <input wire:model="interest_score" type="number" min="1" max="10" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Priority</span>
        <select wire:model="priority" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
            @foreach (['low', 'medium', 'high', 'urgent'] as $priorityOption)
                <option value="{{ $priorityOption }}">{{ str($priorityOption)->title() }}</option>
            @endforeach
        </select>
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">FNA Status</span>
        <select wire:model="fna_status" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
            @foreach ($fnaStatuses as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </label>
    @if (! empty($includeStatus))
        <label class="block">
            <span class="text-sm font-semibold text-slate-700">Next Follow-Up</span>
            <input wire:model="next_follow_up_at" type="datetime-local" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
        </label>
    @endif
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Referral Source</span>
        <input wire:model="referral_source_name" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Campaign</span>
        <input wire:model="campaign_name" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
</div>

<label class="mt-4 block">
    <span class="text-sm font-semibold text-slate-700">Notes Summary</span>
    <textarea wire:model="notes_summary" rows="4" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm"></textarea>
</label>
