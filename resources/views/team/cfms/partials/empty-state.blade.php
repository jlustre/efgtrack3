<div x-show="filteredCfms.length === 0" x-cloak class="text-center py-12 bg-gray-900/30 rounded-2xl border border-gray-800 mb-10">
    <div class="text-5xl mb-4 opacity-60">👥</div>
    <div class="text-gray-400">No CFMs found matching your criteria.</div>
    <button type="button" @click="clearFilters()" class="mt-4 text-amber-400 hover:text-amber-300 text-sm font-medium">Clear Filters</button>
</div>
