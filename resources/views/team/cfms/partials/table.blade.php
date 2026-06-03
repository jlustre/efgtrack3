<div x-show="viewMode === 'table'" x-cloak class="hidden md:block overflow-x-auto rounded-2xl border border-gray-800 bg-gray-900/20 backdrop-blur-sm mb-10">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-gray-900/80 border-b border-gray-800 text-gray-400">
            <tr>
                <th class="px-4 py-3">CFM</th>
                <th class="px-4 py-3">Hierarchy</th>
                <th class="px-4 py-3">Load / Status</th>
                <th class="px-4 py-3">Completion Rate</th>
                <th class="px-4 py-3">Overdue Tasks</th>
                <th class="px-4 py-3">Next Available</th>
                <th class="px-4 py-3">Score</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
            <template x-for="cfm in filteredCfms" :key="cfm.id">
                <tr class="hover:bg-gray-800/30 transition cursor-pointer" @click="selectCfm(cfm)">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-600 to-amber-800 flex items-center justify-center font-bold text-black text-sm" x-text="cfm.initials"></div>
                            <div>
                                <div class="font-medium text-white" x-text="cfm.name"></div>
                                <div class="text-xs text-gray-400" x-text="cfm.rank"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs" :class="hierarchyBadgeClass(cfm)" x-text="cfm.hierarchy"></span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 min-w-[140px]">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" :class="statusDotClass(cfm.statusColor)"></span>
                            <span class="text-gray-300 text-xs" x-text="cfm.activeApprentices + '/' + cfm.maxApprentices + ' apprentices'"></span>
                            <div class="w-16 bg-gray-700 rounded-full h-1 flex-1 max-w-[4rem]">
                                <div class="bg-amber-400 h-1 rounded-full" :style="'width:' + loadWidth(cfm) + '%'"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3"><span class="text-green-400" x-text="cfm.completionRate + '%'"></span></td>
                    <td class="px-4 py-3" :class="cfm.overdueTasks > 0 ? 'text-red-400' : 'text-gray-400'" x-text="cfm.overdueTasks"></td>
                    <td class="px-4 py-3 text-xs text-gray-300" x-text="cfm.nextAvailable"></td>
                    <td class="px-4 py-3">
                        <div class="px-2 py-1 rounded-full text-center text-xs font-bold" :class="scoreClass(cfm.score)" x-text="cfm.score + '/100'"></div>
                    </td>
                    <td class="px-4 py-3">
                        <button type="button" @click.stop="openAssign(cfm)" class="text-amber-400 hover:text-amber-300 text-sm font-medium">Assign</button>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
</div>

<!-- Mobile table fallback -->
<div x-show="viewMode === 'table'" x-cloak class="md:hidden grid gap-4 mb-10">
    <template x-for="cfm in filteredCfms" :key="'mt-' + cfm.id">
        <div class="bg-gray-900/40 border border-gray-800 rounded-2xl p-4" @click="selectCfm(cfm)">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-amber-600 to-amber-800 flex items-center justify-center font-bold text-black text-sm" x-text="cfm.initials"></div>
                    <div>
                        <div class="font-medium text-white" x-text="cfm.name"></div>
                        <div class="text-xs text-gray-400" x-text="cfm.rank"></div>
                    </div>
                </div>
                <span class="px-2 py-1 rounded-full text-xs font-bold" :class="scoreClass(cfm.score)" x-text="cfm.score"></span>
            </div>
            <div class="mt-3 flex justify-between text-xs text-gray-400">
                <span x-text="cfm.hierarchy"></span>
                <span x-text="cfm.activeApprentices + '/' + cfm.maxApprentices"></span>
            </div>
            <button type="button" @click.stop="openAssign(cfm)" class="mt-3 w-full bg-amber-600/20 text-amber-400 border border-amber-500/30 rounded-xl py-2 text-sm">Assign</button>
        </div>
    </template>
</div>
