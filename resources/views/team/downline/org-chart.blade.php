<x-app-layout>
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-400 bg-gradient-to-br from-[#05070B] via-[#111827] to-[#2A2110] p-6 text-white shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Organizational Chart</p>
                    <h1 class="mt-2 text-2xl font-semibold">Executive Team Structure</h1>
                    <p class="mt-2 text-sm text-slate-300">Leadership view for branch depth, team health, licensing, mentorship, and training summaries.</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('team.tree') }}" class="rounded-lg border border-white/20 px-4 py-2 text-sm font-semibold hover:bg-white/10">Tree View</a>
                    <a href="{{ route('team.table') }}" class="rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A]">Table View</a>
                </div>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
            @foreach ([
                'Total Members' => $branchSummary['total_team'],
                'Direct Recruits' => $branchSummary['direct_recruits'],
                'Active Members' => $branchSummary['active_associates'],
                'Licensed' => $branchSummary['licensed_associates'],
                'New This Month' => $branchSummary['new_this_month'],
                'Training Avg' => $branchSummary['training_average'].'%',
            ] as $label => $value)
                <div class="rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-4 shadow-sm">
                    <div class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $label }}</div>
                    <div class="mt-2 text-xl font-semibold text-[#0B1F3A]">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        <div class="overflow-x-auto rounded-lg border border-slate-400 bg-white p-6 shadow-sm">
            <div class="min-w-[960px]">
                <div class="flex justify-center">
                    <div class="w-80 rounded-lg border border-[#C8A24A] bg-[#05070B] p-5 text-white shadow-lg">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#FFF4CF] font-bold text-[#0B1F3A]">{{ $root['avatar'] }}</div>
                            <div>
                                <div class="font-semibold">{{ $root['name'] }}</div>
                                <div class="text-xs text-slate-300">{{ $root['rank_name'] }}</div>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="rounded-lg bg-white/10 p-2"><strong class="text-[#C8A24A]">{{ $root['metrics']['total_downline'] }}</strong><div>Team</div></div>
                            <div class="rounded-lg bg-white/10 p-2"><strong class="text-[#C8A24A]">{{ $root['metrics']['direct_recruits'] }}</strong><div>Direct</div></div>
                            <div class="rounded-lg bg-white/10 p-2"><strong class="text-[#C8A24A]">{{ $root['progress']['training'] }}%</strong><div>Training</div></div>
                        </div>
                    </div>
                </div>

                <div class="mx-auto mt-5 h-8 w-px bg-[#C8A24A]"></div>
                <div class="mx-auto h-px max-w-5xl bg-[#C8A24A]"></div>

                <div class="mt-8 grid gap-5 lg:grid-cols-3">
                    @forelse ($leaders as $leader)
                        <div class="rounded-lg border border-slate-400 bg-gradient-to-br from-white via-slate-50 to-[#FFF9EA] p-5 shadow-sm">
                            <div class="flex items-start gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-full border border-[#C8A24A] bg-[#FFF4CF] text-sm font-bold text-[#0B1F3A]">{{ $leader['avatar'] }}</div>
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('team.member', $leader['id']) }}" class="font-semibold text-[#0B1F3A] hover:text-[#8A6A1F]">{{ $leader['name'] }}</a>
                                    <div class="mt-1 flex items-center gap-2 text-xs text-slate-500">
                                        @include('team.downline.partials.member-badge', ['member' => $leader])
                                        <span>{{ str($leader['role'])->replace('-', ' ')->title() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                                <div class="rounded-lg border border-slate-300 bg-white p-3"><div class="font-bold text-[#0B1F3A]">{{ $leader['metrics']['total_downline'] }}</div><div class="text-xs text-slate-500">Team Size</div></div>
                                <div class="rounded-lg border border-slate-300 bg-white p-3"><div class="font-bold text-[#0B1F3A]">{{ $leader['metrics']['direct_recruits'] }}</div><div class="text-xs text-slate-500">Directs</div></div>
                                <div class="rounded-lg border border-slate-300 bg-white p-3"><div class="font-bold text-[#0B1F3A]">{{ $leader['active_associates'] }}</div><div class="text-xs text-slate-500">Active</div></div>
                                <div class="rounded-lg border border-slate-300 bg-white p-3"><div class="font-bold text-[#0B1F3A]">{{ $leader['licensed_associates'] }}</div><div class="text-xs text-slate-500">Licensed</div></div>
                            </div>
                            <div class="mt-4">
                                @include('team.downline.partials.progress-line', ['label' => 'Branch Training', 'value' => $leader['progress']['training']])
                            </div>
                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('team.member.org-chart', $leader['id']) }}" class="rounded-lg border border-[#C8A24A] px-3 py-2 text-xs font-bold text-[#0B1F3A] hover:bg-[#FFF4CF]">Open Branch</a>
                                <a href="{{ route('team.member.tree', $leader['id']) }}" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Genealogy</a>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-lg border border-slate-300 bg-slate-50 p-6 text-center text-slate-600 lg:col-span-3">No leader branches under this root yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
