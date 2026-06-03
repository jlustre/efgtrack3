<aside class="rounded-lg border border-[#C8A24A]/25 bg-gradient-to-br from-white via-[#FFF9EA] to-[#F8F3E7] p-4 shadow-sm">
    <div class="mb-3 flex flex-wrap items-center gap-2">
        <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Insights</p>
        <h4 class="text-sm font-semibold text-[#0B1F3A]">AI-Powered Task Suggestions</h4>
        <span class="ml-auto rounded-full bg-[#C8A24A]/15 px-2 py-0.5 text-[10px] font-bold text-[#8A6A1F]">Coming Soon</span>
    </div>
    <div class="grid gap-2 sm:grid-cols-2">
        <template x-for="sug in aiSuggestions" :key="sug.text">
            <div class="flex items-start gap-2.5 rounded-md border border-slate-200 bg-white p-3 shadow-sm">
                <span class="text-lg leading-none" x-text="sug.icon"></span>
                <span class="text-xs leading-relaxed text-slate-600" x-text="sug.text"></span>
            </div>
        </template>
    </div>
</aside>
