<div class="overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-[#FFF9EA] shadow-sm">
    <div class="flex flex-col gap-4 bg-[#0B1F3A] px-6 py-6 text-white lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Prospect Management</p>
            <h1 class="mt-2 text-2xl font-semibold">Private CRM workspace</h1>
            <p class="mt-2 max-w-4xl text-sm leading-6 text-slate-200">
                Manage prospects, follow-ups, appointments, pipeline stages, and controlled sharing with mentors or leaders.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('team.prospects.create') }}" class="inline-flex items-center justify-center rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] shadow-sm transition hover:bg-[#D8B85F]">
                Add Prospect
            </a>
            @can('import prospects')
                <a href="{{ route('team.prospects.import') }}" class="inline-flex items-center justify-center rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                    Import CSV
                </a>
            @endcan
            @can('export prospects')
                <a href="{{ route('team.prospects.export') }}" class="inline-flex items-center justify-center rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                    Export CSV
                </a>
            @endcan
        </div>
    </div>

    <div class="grid gap-3 border-t border-slate-200/80 bg-gradient-to-br from-white via-slate-50 to-[#F8F3E7] p-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'My Prospects', 'value' => $stats['total'], 'theme' => 'gold', 'subtitle' => 'Active in your CRM', 'href' => route('team.prospects')],
            ['label' => 'Hot Prospects', 'value' => $stats['hot'], 'theme' => 'red', 'subtitle' => 'High-priority leads', 'href' => route('team.prospects', ['prospect_interest' => 'hot'])],
            ['label' => 'Follow-Ups Due', 'value' => $stats['followups_due'], 'theme' => 'amber', 'subtitle' => 'Need action today', 'href' => route('team.prospects.follow-ups')],
            ['label' => 'Appointments', 'value' => $stats['appointments'], 'theme' => 'cyan', 'subtitle' => 'Scheduled meetings', 'href' => route('team.prospects.appointments')],
            ['label' => 'Shared With Me', 'value' => $stats['shared_with_me'], 'theme' => 'violet', 'subtitle' => 'Prospects from others', 'href' => route('team.prospects.shared-with-me')],
            ['label' => 'Shared By Me', 'value' => $stats['shared_by_me'], 'theme' => 'navy', 'subtitle' => 'Shared with team', 'href' => route('team.prospects.shared-by-me')],
            ['label' => 'Conversion Rate', 'value' => $stats['conversion_rate'].'%', 'theme' => 'emerald', 'subtitle' => 'Prospect to client', 'href' => route('team.prospects.analytics')],
        ] as $card)
            <a href="{{ $card['href'] }}" class="block rounded-lg transition hover:scale-[1.01] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C8A24A]">
                <x-tracker-stat-card
                    :label="$card['label']"
                    :value="$card['value']"
                    :subtitle="$card['subtitle']"
                    :theme="$card['theme']"
                />
            </a>
        @endforeach
    </div>
</div>
