<x-app-layout>
    <section class="space-y-6">
        <div class="overflow-hidden rounded-lg border border-slate-400 bg-[#05070B] shadow-sm">
            <div class="bg-gradient-to-br from-[#05070B] via-[#111827] to-[#2A2110] px-6 py-7 text-white">
                <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Downline Management</p>
                <div class="mt-3 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold">Team Command Center</h1>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-200">
                            View hierarchy, leadership depth, rank movement, licensing readiness, mentorship coverage, and team growth from one protected workspace.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('team.tree') }}" class="rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A]">Tree View</a>
                        <a href="{{ route('team.org-chart') }}" class="rounded-lg border border-white/25 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Org Chart</a>
                        <a href="{{ route('team.table') }}" class="rounded-lg border border-white/25 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Table View</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Total Team', 'value' => $stats['total_team']],
                ['label' => 'Direct Recruits', 'value' => $stats['direct_recruits']],
                ['label' => 'Active Associates', 'value' => $stats['active_associates']],
                ['label' => 'Licensed Associates', 'value' => $stats['licensed_associates']],
                ['label' => 'New This Month', 'value' => $stats['new_this_month']],
                ['label' => 'Pending Licensing', 'value' => $stats['pending_licensing']],
                ['label' => 'CFM Assigned', 'value' => $stats['cfm_assigned']],
                ['label' => 'Training Average', 'value' => $stats['training_average'].'%'],
            ] as $card)
                <div class="rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-5 shadow-sm">
                    <div class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $card['label'] }}</div>
                    <div class="mt-3 text-2xl font-semibold text-[#0B1F3A]">{{ $card['value'] }}</div>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-lg border border-slate-400 bg-gradient-to-br from-white via-slate-50 to-[#FFF9EA] p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-[#0B1F3A]">Rank Distribution</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($rankDistribution as $rank => $total)
                        <div class="flex items-center justify-between rounded-lg border border-slate-300 bg-white/85 px-4 py-3">
                            <span class="font-semibold text-[#0B1F3A]">{{ $rank }}</span>
                            <span class="rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2.5 py-1 text-xs font-bold">{{ $total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No rank data yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-lg border border-slate-400 bg-gradient-to-br from-white via-slate-50 to-[#FFF9EA] p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-[#0B1F3A]">Country Distribution</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($countryDistribution as $country => $total)
                        <div class="flex items-center justify-between rounded-lg border border-slate-300 bg-white/85 px-4 py-3">
                            <span class="font-semibold text-[#0B1F3A]">{{ $country }}</span>
                            <span class="rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2.5 py-1 text-xs font-bold">{{ $total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No country data yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-6 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Visible Team Members</h2>
                    <p class="mt-1 text-sm text-slate-600">Privacy-filtered preview of members you are allowed to see.</p>
                </div>
                <a href="{{ route('team.table') }}" class="rounded-lg border border-[#C8A24A] bg-white px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#FFF9EA]">Open Full Table</a>
            </div>

            <div class="mt-5 overflow-x-auto rounded-lg border border-slate-300 bg-white/85">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Member</th>
                            <th class="px-4 py-3">Rank</th>
                            <th class="px-4 py-3">Sponsor</th>
                            <th class="px-4 py-3">CFM</th>
                            <th class="px-4 py-3">Directs</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($members as $member)
                            <tr>
                                <td class="px-4 py-3">
                                    <a href="{{ route('team.member', $member) }}" class="font-semibold text-[#0B1F3A] hover:text-[#8A6A1F]">{{ $member->name }}</a>
                                    <div class="text-xs text-slate-500">
                                        {{ $member->profile?->city ?? 'City not set' }}
                                        @if ($member->profile?->country)
                                            &middot; {{ $member->profile->country }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">@include('team.downline.partials.member-badge', ['member' => $member])</td>
                                <td class="px-4 py-3 text-slate-600">{{ $member->sponsor?->name ?? 'None' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $member->mentor?->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $member->direct_recruits_count }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $member->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5">{{ $members->links() }}</div>
        </div>
    </section>
</x-app-layout>
