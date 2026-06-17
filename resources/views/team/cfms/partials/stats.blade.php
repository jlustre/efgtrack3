<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total CFMs</p>
        <p class="mt-2 text-2xl font-bold text-[#0B1F3A]" x-text="stats.total"></p>
        <p class="mt-1 text-xs text-slate-500">Across accessible hierarchies</p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Available CFMs</p>
        <p class="mt-2 text-2xl font-bold text-emerald-700" x-text="stats.available"></p>
        <p class="mt-1 text-xs text-slate-500">0–2 apprentices</p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Busy / Overloaded</p>
        <p class="mt-2 text-2xl font-bold text-orange-700" x-text="busyOverloadedCount"></p>
        <p class="mt-1 text-xs text-slate-500">6+ apprentices</p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">My Hierarchy</p>
        <p class="mt-2 text-2xl font-bold text-[#0B1F3A]" x-text="stats.myHierarchy"></p>
        <p class="mt-1 text-xs text-[#8A6A1F]">Full access</p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">External Hierarchy</p>
        <p class="mt-2 text-2xl font-bold text-sky-700" x-text="stats.externalHierarchy"></p>
        <p class="mt-1 text-xs text-slate-500">May require approval</p>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Active Apprentices</p>
        <p class="mt-2 text-2xl font-bold text-[#0B1F3A]" x-text="stats.activeApprentices"></p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pending FAP Assignments</p>
        <p class="mt-2 text-2xl font-bold text-[#0B1F3A]" x-text="stats.pendingFap"></p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Avg Mentor Load</p>
        <p class="mt-2 text-2xl font-bold text-[#0B1F3A]"><span x-text="stats.averageLoad"></span><span class="text-sm text-slate-500">/6</span></p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">FAP Completion Rate</p>
        <p class="mt-2 text-2xl font-bold text-emerald-700"><span x-text="stats.fapCompletionRate"></span>%</p>
    </div>
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Avg Weekly Availability</p>
        <p class="mt-2 text-2xl font-bold text-[#0B1F3A]"><span x-text="stats.avgWeeklyAvailabilityHours"></span>h</p>
    </div>
</div>
