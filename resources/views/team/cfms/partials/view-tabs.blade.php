<div class="flex flex-wrap gap-2 border-b border-slate-200 pb-1">
    <button
        type="button"
        @click="viewMode = 'table'"
        class="rounded-t-lg px-4 py-2 text-sm font-semibold transition"
        :class="viewMode === 'table' ? 'border-b-2 border-[#C8A24A] text-[#0B1F3A]' : 'text-slate-500 hover:text-[#0B1F3A]'"
    >Table View</button>
    <button
        type="button"
        @click="viewMode = 'cards'"
        class="rounded-t-lg px-4 py-2 text-sm font-semibold transition"
        :class="viewMode === 'cards' ? 'border-b-2 border-[#C8A24A] text-[#0B1F3A]' : 'text-slate-500 hover:text-[#0B1F3A]'"
    >Card View</button>
    <button
        type="button"
        @click="viewMode = 'compare'"
        class="rounded-t-lg px-4 py-2 text-sm font-semibold transition"
        :class="viewMode === 'compare' ? 'border-b-2 border-[#C8A24A] text-[#0B1F3A]' : 'text-slate-500 hover:text-[#0B1F3A]'"
    >Compare</button>
</div>
