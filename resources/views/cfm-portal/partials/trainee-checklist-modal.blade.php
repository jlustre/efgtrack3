<div
    x-show="showTraineeChecklistModal"
    x-cloak
    class="fixed inset-0 z-[60] overflow-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
    @keydown.escape.window="closeTraineeChecklistModal()"
>
    <div
        class="bg-gray-900 border border-gray-800 rounded-2xl max-w-4xl w-full shadow-2xl max-h-[90vh] flex flex-col"
        @click.stop
    >
        <div class="flex items-start justify-between gap-4 border-b border-gray-800 px-6 py-5">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-400">Mentoring Checklist</p>
                <h3 class="mt-1 text-xl font-bold text-white" x-text="checklistModalData?.trainee?.name || 'Trainee progress'"></h3>
                <p class="mt-1 text-sm text-gray-400" x-show="checklistModalData?.trainee?.rank">
                    Rank: <span x-text="checklistModalData?.trainee?.rank"></span>
                </p>
            </div>
            <button
                type="button"
                @click="closeTraineeChecklistModal()"
                class="text-gray-400 hover:text-white text-2xl leading-none shrink-0"
                aria-label="Close checklist modal"
            >&times;</button>
        </div>

        <div class="px-6 py-4 border-b border-gray-800" x-show="checklistModalData && ! checklistModalLoading">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-gray-300">Overall progress</span>
                        <span class="font-bold text-amber-400" x-text="`${checklistModalData?.stats?.percent ?? 0}%`"></span>
                    </div>
                    <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-gray-800">
                        <div
                            class="h-full rounded-full bg-amber-500 transition-all"
                            :style="`width: ${checklistModalData?.stats?.percent ?? 0}%`"
                        ></div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        <span x-text="checklistModalData?.stats?.completed ?? 0"></span> accomplished ·
                        <span x-text="checklistModalData?.stats?.remaining ?? 0"></span> remaining ·
                        <span x-text="checklistModalData?.stats?.total ?? 0"></span> total
                    </p>
                </div>

                <div class="flex rounded-xl border border-gray-800 bg-gray-950/60 p-1 text-xs font-semibold">
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 transition"
                        :class="checklistModalView === 'accomplished' ? 'bg-amber-500 text-black' : 'text-gray-400 hover:text-white'"
                        @click="checklistModalView = 'accomplished'"
                    >
                        Accomplished
                    </button>
                    <button
                        type="button"
                        class="rounded-lg px-3 py-1.5 transition"
                        :class="checklistModalView === 'remaining' ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white'"
                        @click="checklistModalView = 'remaining'"
                    >
                        Remaining
                    </button>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-5">
            <div x-show="checklistModalLoading" class="py-16 text-center">
                <div class="mx-auto h-8 w-8 animate-spin rounded-full border-2 border-amber-500 border-t-transparent"></div>
                <p class="mt-4 text-sm text-gray-400">Loading checklist progress…</p>
            </div>

            <div x-show="checklistModalError && ! checklistModalLoading" class="rounded-xl border border-red-500/30 bg-red-900/20 px-4 py-3 text-sm text-red-300">
                <span x-text="checklistModalError"></span>
            </div>

            <template x-if="checklistModalData && ! checklistModalLoading && checklistModalView === 'accomplished'">
                <div class="space-y-4">
                    <template x-if="accomplishedPhases().length === 0">
                        <div class="rounded-xl border border-gray-800 bg-gray-950/40 px-4 py-10 text-center">
                            <p class="text-sm font-semibold text-white">No items completed yet</p>
                            <p class="mt-2 text-sm text-gray-500">Start tracking mentoring from the full checklist page.</p>
                        </div>
                    </template>

                    <template x-for="phase in accomplishedPhases()" :key="`accomplished-phase-${phase.phase_number}`">
                        <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-950/40">
                            <div class="border-b border-gray-800 bg-gray-900/70 px-4 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <span class="text-xs font-bold text-amber-400" x-text="`Phase ${phase.phase_number}`"></span>
                                        <h4 class="mt-1 text-sm font-semibold text-white" x-text="phase.phase_title"></h4>
                                        <p class="mt-0.5 text-xs text-gray-500" x-show="phase.phase_target" x-text="`Target: ${phase.phase_target}`"></p>
                                    </div>
                                    <span class="text-xs font-semibold text-emerald-400" x-text="`${phase.completed}/${phase.total}`"></span>
                                </div>
                            </div>

                            <template x-for="section in phase.sections" :key="`accomplished-section-${phase.phase_number}-${section.title}`">
                                <div class="border-b border-gray-800/70 last:border-b-0">
                                    <div class="bg-gray-900/40 px-4 py-2">
                                        <h5 class="text-xs font-semibold uppercase tracking-wide text-gray-500" x-text="section.title"></h5>
                                    </div>
                                    <ul class="divide-y divide-gray-800/70">
                                        <template x-for="item in section.items" :key="`accomplished-item-${item.id}`">
                                            <li class="flex items-start gap-3 px-4 py-3">
                                                <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-400">
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true">
                                                        <path d="M20 6 9 17l-5-5" />
                                                    </svg>
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="text-sm text-white" x-text="item.title"></p>
                                                    <p class="mt-1 text-xs text-gray-500" x-show="item.completed_at" x-text="`Completed ${item.completed_at}`"></p>
                                                    <p class="mt-1 text-xs text-gray-400" x-show="item.notes" x-text="item.notes"></p>
                                                </div>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="checklistModalData && ! checklistModalLoading && checklistModalView === 'remaining'">
                <div class="space-y-4">
                    <template x-if="remainingPhases().length === 0">
                        <div class="rounded-xl border border-emerald-500/20 bg-emerald-950/20 px-4 py-10 text-center">
                            <p class="text-sm font-semibold text-emerald-300">All checklist items are complete</p>
                            <p class="mt-2 text-sm text-gray-400">This trainee has finished every mentoring milestone.</p>
                        </div>
                    </template>

                    <template x-for="phase in remainingPhases()" :key="`remaining-phase-${phase.phase_number}`">
                        <div class="overflow-hidden rounded-xl border border-gray-800 bg-gray-950/40">
                            <div class="border-b border-gray-800 bg-gray-900/70 px-4 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <span class="text-xs font-bold text-gray-500" x-text="`Phase ${phase.phase_number}`"></span>
                                        <h4 class="mt-1 text-sm font-semibold text-white" x-text="phase.phase_title"></h4>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-400" x-text="`${phase.completed}/${phase.total}`"></span>
                                </div>
                            </div>

                            <template x-for="section in phase.sections" :key="`remaining-section-${phase.phase_number}-${section.title}`">
                                <div class="border-b border-gray-800/70 last:border-b-0">
                                    <div class="bg-gray-900/40 px-4 py-2">
                                        <h5 class="text-xs font-semibold uppercase tracking-wide text-gray-500" x-text="section.title"></h5>
                                    </div>
                                    <ul class="divide-y divide-gray-800/70">
                                        <template x-for="item in section.items" :key="`remaining-item-${item.id}`">
                                            <li class="flex items-start gap-3 px-4 py-3">
                                                <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 rounded-full border border-gray-700"></span>
                                                <p class="text-sm text-gray-300" x-text="item.title"></p>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <div class="flex items-center justify-between gap-3 border-t border-gray-800 px-6 py-4">
            <p class="text-xs text-gray-500">Read-only summary. Use the full checklist page to update progress.</p>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="closeTraineeChecklistModal()"
                    class="rounded-lg border border-gray-700 px-4 py-2 text-sm font-semibold text-gray-300 transition hover:border-gray-600 hover:text-white"
                >
                    Close
                </button>
                <a
                    x-show="checklistModalData?.checklist_url"
                    :href="checklistModalData?.checklist_url"
                    class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-bold text-black transition hover:bg-amber-400"
                >
                    Open full checklist
                </a>
            </div>
        </div>
    </div>
</div>
