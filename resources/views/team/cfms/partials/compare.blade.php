<div x-show="viewMode === 'compare'" x-cloak class="mb-10">
    <h3 class="text-white font-bold mb-4">Compare CFMs (max 3)</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <template x-for="i in 3" :key="'cmp-' + i">
            <div class="bg-gray-900/40 border border-gray-800 rounded-2xl p-4">
                <select class="bg-gray-800 border border-gray-700 rounded-xl px-3 py-2 w-full text-sm text-gray-200 focus:border-amber-500 focus:outline-none" x-model="compareCfms[i - 1]">
                    <option value="">Select CFM</option>
                    <template x-for="cfm in cfms" :key="'opt-' + i + '-' + cfm.id">
                        <option :value="cfm.id" x-text="cfm.name"></option>
                    </template>
                </select>
                <div x-show="compareCfms[i - 1]" class="text-xs text-gray-300 mt-3 space-y-2" x-text="getCompareDetails(i - 1)"></div>
            </div>
        </template>
    </div>
</div>
