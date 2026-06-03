<div class="bg-[#0B1F3A] px-6 py-6 text-white">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="min-w-0">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Task Management</p>
            <h1 class="mt-2 text-2xl font-semibold">Organize team priorities</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-200">
                Follow-ups, training actions, mentorship tasks, and rank advancement priorities in one place.
            </p>
            <p class="mt-3 text-xs text-slate-300">{{ $todayLabel }} · {{ $user->roles->pluck('name')->first() ?? 'member' }} · {{ $user->team?->name ?? 'Unassigned' }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" @click="showFilters = !showFilters" class="inline-flex items-center gap-1.5 rounded-md border border-white/20 bg-white/10 px-3.5 py-2 text-sm text-slate-100 transition hover:border-[#C8A24A] hover:bg-white/15">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
                Filters
            </button>
            <button type="button" @click="activeView = 'my'" class="inline-flex items-center gap-1.5 rounded-md border border-[#C8A24A]/50 px-3.5 py-2 text-sm font-medium text-[#C8A24A] transition hover:bg-[#C8A24A]/10">
                My Tasks
            </button>
            <button type="button" @click="activeView = 'team'" class="inline-flex items-center gap-1.5 rounded-md border border-[#C8A24A]/50 px-3.5 py-2 text-sm font-medium text-[#C8A24A] transition hover:bg-[#C8A24A]/10">
                Team Tasks
            </button>
            <button type="button" @click="showNewTask = true" class="inline-flex items-center gap-1.5 rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] shadow-sm transition hover:bg-[#D8B75F]">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                New Task
            </button>
        </div>
    </div>
</div>
