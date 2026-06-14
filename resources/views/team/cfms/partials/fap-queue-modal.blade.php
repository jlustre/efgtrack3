<div
    x-show="showFapQueueModal"
    x-cloak
    class="fixed inset-0 z-[60] overflow-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
    @keydown.escape.window="showFapQueueModal = false"
>
    <div class="bg-gray-900 border border-gray-800 rounded-2xl max-w-3xl w-full shadow-2xl max-h-[90vh] flex flex-col" @click.outside="showFapQueueModal = false">
        <div class="flex items-center justify-between border-b border-gray-800 px-6 py-4 shrink-0">
            <div>
                <h3 class="text-xl font-bold text-white">FAP Assignment Queue</h3>
                <p class="text-xs text-gray-500 mt-1">Associates in your downline awaiting CFM assignment</p>
            </div>
            <button type="button" @click="showFapQueueModal = false" class="text-gray-400 hover:text-white text-2xl leading-none">&times;</button>
        </div>

        <div class="px-6 py-3 border-b border-gray-800 bg-gray-900/50 shrink-0">
            <div class="flex flex-wrap gap-4 text-sm">
                <span class="text-gray-400">Pending: <strong class="text-white" x-text="stats.pendingFap"></strong></span>
                <span class="text-gray-400">Showing: <strong class="text-white" x-text="fapQueue.length"></strong></span>
            </div>
        </div>

        <div class="overflow-y-auto flex-1 p-6">
            <div x-show="fapQueue.length === 0" class="text-center py-10 text-gray-500">
                <p class="text-sm">No associates are waiting for a CFM assignment.</p>
            </div>

            <div x-show="fapQueue.length > 0" class="space-y-3">
                <template x-for="associate in fapQueue" :key="'fap-' + associate.id">
                    <div class="rounded-xl border border-gray-800 bg-gray-800/30 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <p class="font-semibold text-white" x-text="associate.name"></p>
                            <p x-show="associate.queueLabel" class="mt-0.5 text-xs text-amber-300/90" x-text="associate.queueLabel"></p>
                            <p class="text-xs text-gray-400 mt-0.5" x-text="associate.email"></p>
                            <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-500">
                                <span class="rounded-full bg-amber-900/30 text-amber-300 px-2 py-0.5" x-text="associate.rank"></span>
                                <span x-text="'Upline: ' + associate.sponsor"></span>
                                <span x-text="(associate.province && associate.province !== '—' ? associate.province + ', ' + (associate.country || 'Canada') + ' · ' : '') + associate.city + ' · ' + associate.timezone"></span>
                            </div>
                        </div>
                        <div class="flex gap-2 shrink-0 flex-wrap">
                            <a :href="associate.profileUrl" class="px-3 py-2 rounded-lg border border-gray-700 text-xs text-gray-300 hover:text-white hover:border-gray-600 transition">View</a>
                            <button
                                type="button"
                                @click="selectAssociateForRecommendations(associate); showFapQueueModal = false"
                                class="px-3 py-2 rounded-lg border border-amber-600/50 text-xs text-amber-300 hover:bg-amber-900/30 transition"
                                :class="String(selectedRecommendationAssociateId) === String(associate.id) ? 'bg-amber-900/40' : ''"
                            >View Matches</button>
                            <button type="button" @click="assignFromQueue(associate)" class="px-3 py-2 rounded-lg bg-amber-600 text-black text-xs font-semibold hover:bg-amber-500 transition">Assign CFM</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="border-t border-gray-800 px-6 py-4 flex gap-3 shrink-0">
            <button type="button" @click="showFapQueueModal = false" class="flex-1 border border-gray-700 py-2.5 rounded-xl text-gray-300 hover:bg-gray-800 transition">Close</button>
            <button type="button" @click="openAssign()" class="flex-1 bg-amber-600 text-black font-bold py-2.5 rounded-xl hover:bg-amber-500 transition">New Assignment</button>
        </div>
    </div>
</div>
