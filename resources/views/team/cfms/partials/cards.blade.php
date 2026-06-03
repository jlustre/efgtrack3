<div x-show="viewMode === 'cards'" x-cloak class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-10">
    <template x-for="cfm in filteredCfms" :key="'card-' + cfm.id">
        <div class="bg-gray-900/40 border border-gray-800 rounded-2xl p-5 hover:shadow-xl hover:border-amber-500/30 transition-all cursor-pointer" @click="selectCfm(cfm)">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-amber-600 to-amber-800 flex items-center justify-center text-lg font-bold text-black" x-text="cfm.initials"></div>
                    <div>
                        <div class="font-bold text-white" x-text="cfm.name"></div>
                        <div class="text-xs text-amber-400" x-text="cfm.rank"></div>
                    </div>
                </div>
                <span class="px-2 py-1 rounded-full text-xs" :class="statusBadgeClass(cfm.statusColor)" x-text="cfm.statusText"></span>
            </div>
            <span class="mt-2 inline-block px-2 py-0.5 rounded-full text-xs" :class="hierarchyBadgeClass(cfm)" x-text="cfm.hierarchy"></span>
            <p x-show="cfm.hierarchyNotice" class="mt-2 text-[10px] text-amber-300/90" x-text="cfm.hierarchyNotice"></p>
            <div class="mt-3 flex justify-between text-xs text-gray-400">
                <span>Active: <span class="text-white" x-text="cfm.activeApprentices + '/' + cfm.maxApprentices"></span></span>
                <span>Completion: <span class="text-green-400" x-text="cfm.completionRate + '%'"></span></span>
            </div>
            <div class="mt-2 w-full bg-gray-700 rounded-full h-1.5">
                <div class="bg-amber-400 h-1.5 rounded-full" :style="'width:' + loadWidth(cfm) + '%'"></div>
            </div>
            <div class="mt-3 flex justify-between items-center text-xs">
                <span class="text-gray-400">Next: <span class="text-white" x-text="cfm.nextAvailable"></span></span>
                <span class="px-2 py-1 rounded-full font-bold" :class="scoreClass(cfm.score)" x-text="cfm.score + '/100'"></span>
            </div>
            <button type="button" @click.stop="openAssign(cfm)" class="mt-4 w-full bg-amber-600/20 hover:bg-amber-600/40 text-amber-400 border border-amber-500/30 rounded-xl py-2 text-sm transition-all">
                Assign Associate
            </button>
        </div>
    </template>
</div>
