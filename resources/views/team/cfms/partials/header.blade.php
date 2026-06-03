<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <p class="text-sm font-semibold uppercase tracking-wide text-amber-500">Certified Field Mentor Management</p>
        <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight mt-1">Certified Field Mentors</h1>
        <p class="text-gray-400 mt-1 max-w-3xl">Review mentor availability, workload, hierarchy source, apprenticeship capacity, and assignment readiness before assigning a new associate to the Field Apprenticeship Program.</p>
        <p class="mt-2 text-xs text-gray-500">{{ $todayLabel }} · {{ $user->roles->pluck('name')->first() ?? 'member' }} · {{ $user->team?->name ?? 'Unassigned' }}</p>
    </div>
    @include('team.cfms.partials.header-actions')
</div>
