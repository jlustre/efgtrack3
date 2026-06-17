<div class="rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
        <div>
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Activity Scorecard</h2>
            <p class="text-sm text-slate-600">{{ $scorecard['period_label'] }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach ($periods as $key => $label)
                <button type="button" wire:click="$set('periodType', '{{ $key }}')" @class([
                    'rounded-full px-3 py-1 text-xs font-semibold',
                    'bg-[#0B1F3A] text-white' => $periodType === $key,
                    'border border-slate-300 text-slate-600' => $periodType !== $key,
                ])>{{ $label }}</button>
            @endforeach
        </div>
    </div>
    <div class="p-6">
        <div class="mb-6 flex items-center justify-between rounded-xl border border-[#C8A24A]/30 bg-[#FFF9EA]/40 px-5 py-4">
            <span class="text-sm font-semibold text-[#0B1F3A]">Overall activity score</span>
            <span class="text-3xl font-bold text-[#8A6A1F]">{{ $scorecard['overall_score'] }}%</span>
        </div>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($scorecard['activities'] as $activity)
                <div wire:key="score-{{ $activity['key'] }}" class="rounded-xl border border-slate-200 p-4">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-[#0B1F3A]">{{ $activity['label'] }}</p>
                        <span class="text-sm font-bold text-slate-600">{{ $activity['percent'] }}%</span>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">{{ number_format($activity['actual'], 0) }} / {{ number_format($activity['target'], 0) }}</p>
                    <div class="mt-3 h-2 rounded-full bg-slate-100">
                        <div class="h-2 rounded-full bg-[#C8A24A]" style="width: {{ $activity['percent'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
