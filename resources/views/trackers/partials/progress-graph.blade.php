<div class="h-full rounded-lg border border-slate-200 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-6 shadow-sm">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Progress Graph</h2>
            <p class="mt-1 text-sm text-slate-600">A quick view of completed, pending, remaining, and required progress.</p>
        </div>
        <div class="text-sm font-semibold text-slate-600">{{ $stats['completed'] }} completed, {{ $stats['pending'] }} pending, {{ $stats['remaining'] }} remaining</div>
    </div>

    <div class="mt-5 space-y-4">
        <div>
            <div class="mb-2 flex items-center justify-between text-sm">
                <span class="font-semibold text-slate-700">{{ $tracker['graphLabel'] }}</span>
                <span class="font-semibold text-[#0B1F3A]">{{ $stats['percent'] }}%</span>
            </div>
            <div class="h-4 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-[#C8A24A] transition-all" style="width: {{ $stats['percent'] }}%"></div>
            </div>
        </div>

        <div>
            <div class="mb-2 flex items-center justify-between text-sm">
                <span class="font-semibold text-slate-700">{{ $tracker['requiredLabel'] }}</span>
                <span class="font-semibold text-[#0B1F3A]">{{ $stats['requiredPercent'] }}%</span>
            </div>
            <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-[#0B1F3A] transition-all" style="width: {{ $stats['requiredPercent'] }}%"></div>
            </div>
        </div>

        <div>
            <div class="mb-2 flex items-center justify-between text-sm">
                <span class="font-semibold text-slate-700">Pending confirmation</span>
                <span class="font-semibold text-[#0B1F3A]">{{ $stats['pending'] }}</span>
            </div>
            <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-amber-400 transition-all" style="width: {{ $stats['total'] > 0 ? (int) round(($stats['pending'] / $stats['total']) * 100) : 0 }}%"></div>
            </div>
        </div>
    </div>
</div>
