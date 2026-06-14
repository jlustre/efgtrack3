<div class="space-y-6" x-data="prospectKanbanBoard()">
    <div class="overflow-hidden rounded-lg border border-slate-400 bg-gradient-to-br from-white via-slate-50 to-[#FFF9EA] shadow-sm">
        <div class="flex flex-col gap-4 bg-[#0B1F3A] px-6 py-6 text-white md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Prospect Management</p>
                <h1 class="mt-2 text-2xl font-semibold">Pipeline Board</h1>
                <p class="mt-2 text-sm text-slate-200">Drag prospects between stages. Only your active prospects are shown.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <label class="text-sm font-semibold text-[#C8A24A]">
                    Funnel
                    <select wire:model.live="funnelType" class="ml-2 rounded-lg border-[#C8A24A]/40 bg-[#05070B] px-3 py-2 text-sm text-white">
                        @foreach ($funnelTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <a href="{{ route('team.prospects.create') }}" class="rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A]">+ Add Prospect</a>
                <a href="{{ route('team.prospects') }}" class="rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Dashboard</a>
            </div>
        </div>
    </div>

    <div wire:loading.delay.class="opacity-60" class="overflow-x-auto pb-4 transition-opacity">
        <div class="flex min-w-max gap-4">
            @foreach ($stages as $stage)
                @php
                    $stageId = (int) $stage->pipeline_stage_id;
                    $stageProspects = $groupedProspects->get($stageId, collect());
                @endphp
                <div
                    wire:key="stage-{{ $stageId }}"
                    class="w-72 shrink-0 rounded-lg border border-[#C8A24A]/30 bg-gradient-to-b from-[#FFF9EA] to-white shadow-sm transition-shadow"
                    x-on:dragover.prevent="dragOverColumn($event, {{ $stageId }})"
                    x-on:dragleave="dragLeaveColumn($event, {{ $stageId }})"
                    x-on:drop.prevent="dropOnColumn($event, {{ $stageId }})"
                    x-bind:class="dragOverStageId === {{ $stageId }} ? 'ring-2 ring-[#C8A24A] shadow-md' : ''"
                >
                    <div class="border-b border-[#C8A24A]/20 bg-[#0B1F3A] px-4 py-3 text-white">
                        <div class="flex items-center justify-between gap-2">
                            <h2 class="text-sm font-semibold">{{ $stage->name }}</h2>
                            <span class="rounded-full bg-[#C8A24A] px-2 py-0.5 text-xs font-bold text-[#0B1F3A]">{{ $stageProspects->count() }}</span>
                        </div>
                    </div>

                    <div class="space-y-3 p-3 min-h-[12rem]">
                        @forelse ($stageProspects as $prospect)
                            <article
                                wire:key="prospect-{{ $prospect->id }}"
                                draggable="true"
                                x-on:dragstart="startDrag($event, @js($prospect->id))"
                                x-on:dragend="endDrag()"
                                x-bind:class="draggingProspectId === @js($prospect->id) ? 'opacity-50 ring-2 ring-[#C8A24A]' : ''"
                                class="cursor-grab rounded-lg border border-slate-200 bg-white p-3 shadow-sm active:cursor-grabbing hover:border-[#C8A24A]/50"
                            >
                                <div class="flex items-start gap-2">
                                    <button
                                        type="button"
                                        draggable="false"
                                        class="mt-0.5 shrink-0 text-slate-400 hover:text-[#8A6A1F]"
                                        title="Drag to move"
                                        tabindex="-1"
                                    >
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                            <path d="M7 4a1 1 0 100 2 1 1 0 000-2zM7 9a1 1 0 100 2 1 1 0 000-2zM7 14a1 1 0 100 2 1 1 0 000-2zM13 4a1 1 0 100 2 1 1 0 000-2zM13 9a1 1 0 100 2 1 1 0 000-2zM13 14a1 1 0 100 2 1 1 0 000-2z"/>
                                        </svg>
                                    </button>
                                    <div class="min-w-0 flex-1">
                                        <a
                                            href="{{ route('team.prospects.records.show', $prospect) }}"
                                            draggable="false"
                                            class="block text-sm font-semibold text-[#0B1F3A] hover:text-[#8A6A1F]"
                                        >
                                            {{ $prospect->displayName() }}
                                        </a>
                                        @if ($prospect->phone)
                                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $prospect->phone) }}" draggable="false" class="mt-1 inline-flex items-center text-[#8A6A1F] hover:text-[#0B1F3A]" title="Call {{ $prospect->phone }}">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2 flex flex-wrap gap-1">
                                    <span class="rounded-full bg-[#FFF4CF] px-2 py-0.5 text-[10px] font-bold uppercase text-[#8A6A1F]">{{ str($prospect->interest_level)->title() }}</span>
                                    @if ($prospect->priority === 'urgent' || $prospect->priority === 'high')
                                        <span class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold uppercase text-red-700">{{ str($prospect->priority)->title() }}</span>
                                    @endif
                                </div>
                                @if ($prospect->next_follow_up_at)
                                    <p class="mt-2 text-xs text-slate-500">Follow-up {{ $prospect->next_follow_up_at->isPast() ? 'overdue' : 'due' }} {{ $prospect->next_follow_up_at->diffForHumans() }}</p>
                                @endif
                                <div class="mt-3 flex flex-wrap gap-1">
                                    <button
                                        type="button"
                                        draggable="false"
                                        wire:click="$dispatch('open-log-activity-modal', { prospectId: '{{ $prospect->id }}', activityType: 'phone_call' })"
                                        class="rounded border border-slate-200 px-2 py-1 text-[10px] font-semibold text-slate-600 hover:bg-[#FFF9EA]"
                                    >
                                        Log Call
                                    </button>
                                    <button
                                        type="button"
                                        draggable="false"
                                        wire:click="$dispatch('open-log-activity-modal', { prospectId: '{{ $prospect->id }}' })"
                                        class="rounded border border-slate-200 px-2 py-1 text-[10px] font-semibold text-slate-600 hover:bg-[#FFF9EA]"
                                    >
                                        Log Activity
                                    </button>
                                </div>
                            </article>
                        @empty
                            <p class="text-center text-xs text-slate-400 py-6">Drop prospects here</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <livewire:prospects.log-activity-modal />
    <livewire:prospects.log-communication-modal />

    @include('prospects.partials.mobile-quick-actions')
</div>
