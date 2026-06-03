<div class="flex justify-end gap-2 mb-4">
    <button type="button" @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-amber-600/20 text-amber-400 border-amber-500/50' : 'text-gray-400 border-gray-700'" class="px-4 py-1.5 rounded-lg border hover:bg-gray-800 transition text-sm">Table</button>
    <button type="button" @click="viewMode = 'cards'" :class="viewMode === 'cards' ? 'bg-amber-600/20 text-amber-400 border-amber-500/50' : 'text-gray-400 border-gray-700'" class="px-4 py-1.5 rounded-lg border hover:bg-gray-800 transition text-sm">Cards</button>
    <button type="button" @click="viewMode = 'compare'" :class="viewMode === 'compare' ? 'bg-amber-600/20 text-amber-400 border-amber-500/50' : 'text-gray-400 border-gray-700'" class="px-4 py-1.5 rounded-lg border hover:bg-gray-800 transition text-sm">Compare</button>
</div>
