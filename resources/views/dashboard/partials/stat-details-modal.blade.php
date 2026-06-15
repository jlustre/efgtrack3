<div
    x-show="modalOpen"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="dashboard-stat-modal-title"
    @keydown.escape.window="closeModal()"
>
    <div
        class="flex max-h-[90vh] w-full max-w-3xl flex-col overflow-hidden rounded-lg bg-white shadow-xl"
        x-on:click.outside="closeModal()"
    >
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-6 py-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]" x-text="modalScopeLabel"></p>
                <h2 id="dashboard-stat-modal-title" class="text-xl font-semibold text-[#0B1F3A]" x-text="modalTitle"></h2>
            </div>
            <button
                type="button"
                class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:border-[#C8A24A] hover:bg-[#FFF9EA] hover:text-[#0B1F3A]"
                x-on:click="closeModal()"
                aria-label="Close details"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M18 6 6 18M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="overflow-y-auto px-6 py-4">
            <template x-if="loading">
                <div class="rounded-md border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                    Loading members...
                </div>
            </template>

            <template x-if="! loading && error">
                <div class="rounded-md border border-red-200 bg-red-50 px-4 py-6 text-center text-sm text-red-700" x-text="error"></div>
            </template>

            <template x-if="! loading && ! error && members.length === 0">
                <div class="rounded-md border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                    No members match this tracker in your current scope.
                </div>
            </template>

            <template x-if="! loading && ! error && members.length > 0">
                <div class="overflow-x-auto rounded-md border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Member</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Rank</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Complete</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <template x-for="member in members" :key="member.id">
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-[#0B1F3A]" x-text="member.name"></div>
                                        <div class="text-xs text-slate-500" x-text="member.email"></div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2 py-0.5 text-xs font-bold text-[#0B1F3A]" x-text="member.rank"></span>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-[#0B1F3A]" x-text="member.percent + '%'"></td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                            :class="member.percent > 0 ? 'bg-[#C8A24A]/20 text-[#0B1F3A]' : 'bg-slate-100 text-slate-600'"
                                            x-text="member.status"
                                        ></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>
        </div>
    </div>
</div>
