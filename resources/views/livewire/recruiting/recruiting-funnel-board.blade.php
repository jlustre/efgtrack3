<div class="space-y-6" x-data="prospectKanbanBoard()">
    <div class="overflow-x-auto pb-4">
        <div class="flex min-w-max gap-4">
            @foreach ($stages as $stage)
                @php
                    $stageId = (int) $stage->pipeline_stage_id;
                    $stageProspects = $groupedProspects->get($stageId, collect());
                @endphp
                <div
                    wire:key="recruiting-stage-{{ $stageId }}"
                    class="w-72 shrink-0 rounded-lg border border-emerald-200/60 bg-gradient-to-b from-emerald-50/80 to-white shadow-sm"
                    x-on:dragover.prevent="dragOverColumn($event, {{ $stageId }})"
                    x-on:dragleave="dragLeaveColumn($event, {{ $stageId }})"
                    x-on:drop.prevent="dropOnColumn($event, {{ $stageId }})"
                    x-bind:class="dragOverStageId === {{ $stageId }} ? 'ring-2 ring-emerald-500 shadow-md' : ''"
                >
                    <div class="border-b border-emerald-200/50 bg-[#0B1F3A] px-4 py-3 text-white">
                        <div class="flex items-center justify-between gap-2">
                            <h2 class="text-sm font-semibold">{{ $stage->name }}</h2>
                            <span class="rounded-full bg-emerald-400 px-2 py-0.5 text-xs font-bold text-[#0B1F3A]">{{ $stageProspects->count() }}</span>
                        </div>
                    </div>

                    <div class="min-h-[12rem] space-y-3 p-3">
                        @forelse ($stageProspects as $prospect)
                            <article
                                wire:key="recruiting-prospect-{{ $prospect->id }}"
                                draggable="true"
                                x-on:dragstart="startDrag($event, @js($prospect->id))"
                                x-on:dragend="endDrag()"
                                class="cursor-grab rounded-lg border border-slate-200 bg-white p-3 shadow-sm active:cursor-grabbing hover:border-emerald-400/50"
                            >
                                <a
                                    href="{{ route('team.prospects.records.show', $prospect) }}"
                                    draggable="false"
                                    class="text-sm font-semibold text-[#0B1F3A] hover:text-emerald-700"
                                >
                                    {{ $prospect->displayName() }}
                                </a>
                                <div class="mt-2 flex flex-wrap gap-1">
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold uppercase text-emerald-800">{{ str($prospect->interest_level)->title() }}</span>
                                </div>
                                @if ($prospect->next_follow_up_at)
                                    <p class="mt-2 text-xs text-slate-500">
                                        Follow-up {{ $prospect->next_follow_up_at->isPast() ? 'overdue' : 'due' }} {{ $prospect->next_follow_up_at->diffForHumans() }}
                                    </p>
                                @endif
                            </article>
                        @empty
                            <p class="py-6 text-center text-xs text-slate-400">No candidates in this stage</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
