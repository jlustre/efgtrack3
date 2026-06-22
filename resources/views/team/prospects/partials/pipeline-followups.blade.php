@php
    $pipelineMax = max(1, (int) $pipelineSummary->max('prospect_count'));
@endphp

<div class="grid gap-6 xl:grid-cols-2">
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-[#0B1F3A]">Pipeline Summary</h2>
                <p class="mt-1 text-sm text-slate-600">
                    {{ $pipelineSummaryFunnel ?? 'Pipeline' }} lifecycle
                    · {{ $pipelineSummary->sum('prospect_count') }} active prospects across stages
                </p>
            </div>
            <a href="{{ route('team.prospects.pipeline') }}" class="rounded-lg border border-[#C8A24A]/40 bg-[#FFF9EA] px-3 py-1.5 text-sm font-semibold text-[#8A6A1F] transition hover:bg-[#C8A24A]/20">
                Open board
            </a>
        </div>
        <div class="mt-5 space-y-3">
            @forelse ($pipelineSummary as $stage)
                <div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-[#0B1F3A]">{{ $stage->label }}</span>
                        <span class="text-slate-600">{{ $stage->prospect_count }}</span>
                    </div>
                    <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-[#C8A24A]" style="width: {{ max(4, ((int) $stage->prospect_count / $pipelineMax) * 100) }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No pipeline stages configured yet.</p>
            @endforelse
        </div>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-[#0B1F3A]">Follow-Up Center</h2>
                <p class="mt-1 text-sm text-slate-600">Due today or overdue</p>
            </div>
            <a href="{{ route('team.prospects.follow-ups') }}" class="rounded-lg border border-[#C8A24A]/40 bg-[#FFF9EA] px-3 py-1.5 text-sm font-semibold text-[#8A6A1F] transition hover:bg-[#C8A24A]/20">
                View all
            </a>
        </div>
        <ul class="mt-5 divide-y divide-slate-100">
            @forelse ($followUpsDueToday as $followUp)
                <li class="flex items-start justify-between gap-3 py-3 first:pt-0">
                    <div class="min-w-0">
                        @if ($followUp->prospect_id ?? null)
                            <a href="{{ route('team.prospects.records.show', $followUp->prospect_id) }}" class="font-semibold text-[#0B1F3A] hover:text-[#8A6A1F]">
                                {{ trim($followUp->first_name.' '.$followUp->last_name) }}
                            </a>
                        @else
                            <p class="font-semibold text-[#0B1F3A]">{{ trim($followUp->first_name.' '.$followUp->last_name) }}</p>
                        @endif
                        <p class="mt-1 text-sm text-slate-600">{{ $followUp->followup_type ?? 'Follow up' }} · {{ \Illuminate\Support\Carbon::parse($followUp->due_at)->format('M j, g:i A') }}</p>
                    </div>
                    <span class="shrink-0 rounded-full border px-2 py-0.5 text-[10px] font-bold uppercase {{ $priorityClasses[$followUp->priority] ?? $priorityClasses['medium'] }}">
                        {{ $followUp->priority }}
                    </span>
                </li>
            @empty
                <li class="py-6 text-center text-sm text-slate-500">No follow-ups due right now.</li>
            @endforelse
        </ul>
    </section>
</div>
