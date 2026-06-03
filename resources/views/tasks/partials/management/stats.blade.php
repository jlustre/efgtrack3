<div class="grid grid-cols-2 gap-3 p-6 sm:grid-cols-3 lg:grid-cols-6">
    <div class="rounded-lg border border-slate-200 border-t-2 border-t-[#C8A24A] bg-white p-4 shadow-sm">
        <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Total Tasks</p>
        <p class="mt-2 text-2xl font-bold text-[#0B1F3A] sm:text-3xl" x-text="stats.total"></p>
    </div>
    <div class="rounded-lg border border-slate-200 border-t-2 border-t-amber-500 bg-white p-4 shadow-sm">
        <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Due Today</p>
        <p class="mt-2 text-2xl font-bold text-amber-700 sm:text-3xl" x-text="stats.dueToday"></p>
    </div>
    <div class="rounded-lg border border-slate-200 border-t-2 border-t-red-500 bg-white p-4 shadow-sm">
        <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Overdue</p>
        <p class="mt-2 text-2xl font-bold text-red-700 sm:text-3xl" x-text="stats.overdue"></p>
    </div>
    <div class="rounded-lg border border-slate-200 border-t-2 border-t-emerald-500 bg-white p-4 shadow-sm">
        <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Completed This Week</p>
        <p class="mt-2 text-2xl font-bold text-emerald-700 sm:text-3xl" x-text="stats.completedWeek"></p>
    </div>
    <div class="rounded-lg border border-slate-200 border-t-2 border-t-orange-500 bg-white p-4 shadow-sm">
        <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500">High Priority</p>
        <p class="mt-2 text-2xl font-bold text-orange-700 sm:text-3xl" x-text="stats.highPriority"></p>
    </div>
    <div class="col-span-2 rounded-lg border border-[#C8A24A]/25 border-t-2 border-t-[#C8A24A] bg-[#FFF9EA] p-4 shadow-sm sm:col-span-1">
        <p class="text-[11px] font-medium uppercase tracking-wider text-slate-600">Assigned to Me</p>
        <p class="mt-2 text-2xl font-bold text-[#0B1F3A] sm:text-3xl" x-text="stats.assignedToMe"></p>
    </div>
</div>
