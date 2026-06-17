<div x-show="filteredCfms.length === 0" x-cloak class="rounded-xl border border-slate-200 bg-white py-12 text-center shadow-sm">
    <div class="mb-4 text-5xl opacity-60">👥</div>
    <div class="text-slate-600">No CFMs found matching your criteria.</div>
    <button type="button" @click="clearFilters()" class="mt-4 text-sm font-semibold text-[#8A6A1F] hover:text-[#C8A24A]">Clear Filters</button>
</div>
