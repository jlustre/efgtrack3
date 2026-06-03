<div
    x-show="showExportModal"
    x-cloak
    class="fixed inset-0 z-[60] overflow-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
    @keydown.escape.window="showExportModal = false"
>
    <div class="bg-gray-900 border border-gray-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl" @click.outside="showExportModal = false">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-bold text-white">Export CFM Report</h3>
                <p class="text-xs text-gray-500 mt-1">Download mentor directory and workload summary</p>
            </div>
            <button type="button" @click="showExportModal = false" class="text-gray-400 hover:text-white text-2xl leading-none">&times;</button>
        </div>

        <div class="space-y-4">
            <div>
                <label class="text-xs font-medium text-gray-400">Export scope</label>
                <select x-model="exportForm.scope" class="mt-1 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-gray-200 focus:border-amber-500 focus:outline-none">
                    <option value="filtered">Current filtered view</option>
                    <option value="all">All accessible CFMs</option>
                </select>
            </div>

            <div>
                <label class="text-xs font-medium text-gray-400">Format</label>
                <select x-model="exportForm.format" class="mt-1 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-gray-200 focus:border-amber-500 focus:outline-none">
                    <option value="csv">CSV spreadsheet</option>
                </select>
            </div>

            <div class="rounded-xl border border-gray-800 bg-gray-800/30 p-3 space-y-2 text-sm text-gray-300">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Include in export</p>
                <label class="flex items-center gap-2">
                    <input type="checkbox" x-model="exportForm.includeStats" class="rounded border-gray-600 bg-gray-800 text-amber-500">
                    Summary dashboard stats
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" x-model="exportForm.includeWorkload" class="rounded border-gray-600 bg-gray-800 text-amber-500" checked disabled>
                    Workload &amp; readiness columns
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" x-model="exportForm.includeCalendar" class="rounded border-gray-600 bg-gray-800 text-amber-500" checked disabled>
                    Calendar availability columns
                </label>
            </div>

            <div class="rounded-xl border border-amber-500/20 bg-amber-900/10 p-3 text-xs text-gray-400">
                <span class="text-amber-300 font-medium" x-text="exportRows.length"></span> CFM record(s) will be exported.
                External hierarchy CFMs may include limited fields based on visibility permissions.
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" @click="showExportModal = false" class="flex-1 border border-gray-700 py-2.5 rounded-xl text-gray-300 hover:bg-gray-800 transition">Cancel</button>
                <button type="button" @click="exportReport()" :disabled="exportRows.length === 0" class="flex-1 bg-amber-600 text-black font-bold py-2.5 rounded-xl hover:bg-amber-500 transition disabled:opacity-40 disabled:cursor-not-allowed">Download Report</button>
            </div>
        </div>
    </div>
</div>
