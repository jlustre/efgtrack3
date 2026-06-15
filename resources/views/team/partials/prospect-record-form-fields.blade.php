@php
    $selected = fn (string $field, mixed $default = null) => old($field, $prospect->{$field} ?? $default);
    $selectedStageId = old('pipeline_stage_id', $prospect->pipeline_stage_id);
    $selectedSourceId = old('prospect_source_id', $prospect->prospect_source_id);
    $selectedFnaStatus = old('fna_status', $prospect->fna_status ?? 'not_started');
    $nextFollowUp = old('next_follow_up_at', $prospect->next_follow_up_at?->format('Y-m-d\TH:i'));
@endphp

<input type="hidden" name="prospect_funnel_id" value="{{ old('prospect_funnel_id', $prospectFunnelId ?? '') }}">

<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    <label class="block md:col-span-2 xl:col-span-3">
        <span class="text-sm font-semibold text-slate-700">Funnel Type</span>
        <select name="funnel_type" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
            @foreach ($funnelTypes as $value => $label)
                <option value="{{ $value }}" @selected($selected('funnel_type', 'insurance') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('funnel_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </label>

    <label class="block">
        <span class="text-sm font-semibold text-slate-700">First Name</span>
        <input name="first_name" value="{{ $selected('first_name') }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
        @error('first_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Last Name</span>
        <input name="last_name" value="{{ $selected('last_name') }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Preferred Name</span>
        <input name="preferred_name" value="{{ $selected('preferred_name') }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Email</span>
        <input name="email" type="email" value="{{ $selected('email') }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Mobile</span>
        <input name="phone" value="{{ $selected('phone') }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Home Phone</span>
        <input name="home_phone" value="{{ $selected('home_phone') }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Work Phone</span>
        <input name="work_phone" value="{{ $selected('work_phone') }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block xl:col-span-2">
        <span class="text-sm font-semibold text-slate-700">Address</span>
        <input name="address_line_1" value="{{ $selected('address_line_1') }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">City</span>
        <input name="city" value="{{ $selected('city') }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">State / Province</span>
        <input name="state_province" value="{{ $selected('state_province') }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Country</span>
        <input name="country" value="{{ $selected('country') }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Postal Code</span>
        <input name="postal_code" value="{{ $selected('postal_code') }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Pipeline Stage</span>
        <select name="pipeline_stage_id" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
            <option value="">Select stage</option>
            @foreach ($stages as $stage)
                @php $stageId = $stage->pipeline_stage_id ?? $stage->id; @endphp
                <option value="{{ $stageId }}" @selected((string) $selectedStageId === (string) $stageId)>{{ $stage->name }}</option>
            @endforeach
        </select>
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Lead Source</span>
        <select name="prospect_source_id" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
            <option value="">No source</option>
            @foreach ($sources as $source)
                <option value="{{ $source->id }}" @selected((string) $selectedSourceId === (string) $source->id)>{{ $source->name }}</option>
            @endforeach
        </select>
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Status</span>
        <select name="status" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
            @foreach (['active', 'inactive', 'archived'] as $statusOption)
                <option value="{{ $statusOption }}" @selected($selected('status', 'active') === $statusOption)>{{ str($statusOption)->title() }}</option>
            @endforeach
        </select>
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Interest Level</span>
        <select name="interest_level" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
            @foreach (['cold', 'warm', 'hot'] as $level)
                <option value="{{ $level }}" @selected($selected('interest_level', 'warm') === $level)>{{ str($level)->title() }}</option>
            @endforeach
        </select>
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Interest Score (1-10)</span>
        <input name="interest_score" type="number" min="1" max="10" value="{{ $selected('interest_score') }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Priority</span>
        <select name="priority" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
            @foreach (['low', 'medium', 'high', 'urgent'] as $priorityOption)
                <option value="{{ $priorityOption }}" @selected($selected('priority', 'medium') === $priorityOption)>{{ str($priorityOption)->title() }}</option>
            @endforeach
        </select>
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">FNA Status</span>
        <select name="fna_status" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
            @foreach ($fnaStatuses as $value => $label)
                <option value="{{ $value }}" @selected($selectedFnaStatus === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Next Follow-Up</span>
        <input name="next_follow_up_at" type="datetime-local" value="{{ $nextFollowUp }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Referral Source</span>
        <input name="referral_source_name" value="{{ $selected('referral_source_name') }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
    <label class="block">
        <span class="text-sm font-semibold text-slate-700">Campaign</span>
        <input name="campaign_name" value="{{ $selected('campaign_name') }}" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">
    </label>
</div>

<label class="mt-4 block">
    <span class="text-sm font-semibold text-slate-700">Notes Summary</span>
    <textarea name="notes_summary" rows="4" class="mt-1 block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm">{{ $selected('notes_summary') }}</textarea>
</label>
