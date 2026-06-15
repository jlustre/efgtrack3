<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-600">Unified history of activities, calls, notes, and stage changes.</p>
        <select wire:model.live="filter" class="rounded-lg border-slate-300 text-sm">
            <option value="all">All events</option>
            <option value="activity">Activities</option>
            <option value="communication">Calls & communications</option>
            <option value="note">Notes</option>
            <option value="stage">Stage changes</option>
        </select>
    </div>

    @php
        $typeStyles = [
            'activity' => 'bg-blue-100 text-blue-800',
            'communication' => 'bg-emerald-100 text-emerald-800',
            'note' => 'bg-amber-100 text-amber-800',
            'stage' => 'bg-violet-100 text-violet-800',
        ];
        $typeLabels = [
            'activity' => 'Activity',
            'communication' => 'Communication',
            'note' => 'Note',
            'stage' => 'Stage',
        ];
    @endphp

    @forelse ($timeline as $item)
        <article class="rounded-lg border border-slate-200 bg-white/90 px-4 py-3 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $typeStyles[$item['type']] ?? 'bg-slate-100 text-slate-700' }}">
                        {{ $typeLabels[$item['type']] ?? $item['type'] }}
                    </span>
                    <span class="text-sm font-semibold text-[#0B1F3A]">{{ $item['label'] }}</span>
                </div>
                <time class="text-xs text-slate-500">{{ $item['occurred_at'] }}</time>
            </div>
            @if ($item['body'])
                <p class="mt-2 text-sm text-slate-700">{{ $item['body'] }}</p>
            @endif
            <p class="mt-1 text-xs text-slate-500">By {{ $item['actor'] }}</p>
        </article>
    @empty
        <div class="rounded-lg border border-dashed border-slate-300 bg-white/70 px-6 py-10 text-center">
            <p class="text-sm font-semibold text-[#0B1F3A]">No timeline events yet</p>
            <p class="mt-1 text-sm text-slate-500">
                @if ($filter === 'all')
                    Log an activity or add a note to build this prospect's history.
                @else
                    No {{ str($filter)->replace('_', ' ') }} events match this filter.
                @endif
            </p>
        </div>
    @endforelse
</div>
