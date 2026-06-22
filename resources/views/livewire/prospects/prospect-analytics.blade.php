<div class="space-y-6">
    <div class="overflow-hidden rounded-lg border border-slate-400 bg-gradient-to-br from-white via-slate-50 to-[#FFF9EA] shadow-sm">
        <div class="flex flex-col gap-4 bg-[#0B1F3A] px-6 py-6 text-white md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Prospect Management</p>
                <h1 class="mt-2 text-2xl font-semibold">Analytics &amp; Goals</h1>
                <p class="mt-2 text-sm text-slate-200">Pipeline conversion, activity trends, lead sources, and period goals.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('team.prospects') }}" class="rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Dashboard</a>
                <a href="{{ route('team.prospects.pipeline') }}" class="rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A]">Pipeline</a>
            </div>
        </div>

        <div class="grid gap-4 p-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Active Prospects', 'value' => $summary['total'], 'theme' => 'navy'],
                ['label' => 'New (30 days)', 'value' => $summary['new_30d'], 'theme' => 'cyan'],
                ['label' => 'Hot Prospects', 'value' => $summary['hot'], 'theme' => 'red'],
                ['label' => 'Follow-Ups Due', 'value' => $summary['followups_due'], 'theme' => 'amber'],
                ['label' => 'Upcoming Appts', 'value' => $summary['appointments_upcoming'], 'theme' => 'violet'],
                ['label' => 'Conversion Rate', 'value' => $summary['conversion_rate'].'%', 'theme' => 'gold'],
                ['label' => 'Insurance Pipeline', 'value' => $summary['insurance_count'], 'theme' => 'cyan'],
                ['label' => 'Recruiting Pipeline', 'value' => $summary['recruiting_count'], 'theme' => 'emerald'],
            ] as $card)
                <x-tracker-stat-card
                    :label="$card['label']"
                    :value="$card['value']"
                    :theme="$card['theme']"
                />
            @endforeach
        </div>
    </div>

    @if ($teamAggregates['visible'] ?? false)
        <div class="rounded-lg border border-[#0B1F3A]/20 bg-gradient-to-br from-[#0B1F3A] via-[#102A4C] to-[#0B1F3A] p-6 text-white shadow-sm">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-[#C8A24A]">Team Aggregates</p>
                    <h2 class="mt-1 text-lg font-semibold">Downline performance (no PII)</h2>
                    <p class="mt-1 text-sm text-slate-300">{{ $teamAggregates['member_count'] }} team members in scope</p>
                </div>
            </div>
            <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['label' => 'Team Prospects', 'value' => $teamAggregates['total_prospects'], 'theme' => 'navy'],
                    ['label' => 'Team Hot', 'value' => $teamAggregates['hot_prospects'], 'theme' => 'red'],
                    ['label' => 'Follow-Ups Due', 'value' => $teamAggregates['followups_due'], 'theme' => 'amber'],
                    ['label' => 'Avg Conversion', 'value' => $teamAggregates['avg_conversion_rate'].'%', 'theme' => 'gold'],
                ] as $card)
                    <x-tracker-stat-card
                        :label="$card['label']"
                        :value="$card['value']"
                        :theme="$card['theme']"
                    />
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1fr_22rem]">
        <div class="space-y-6">
            <div class="rounded-lg border border-[#C8A24A]/30 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-[#0B1F3A]">Funnel Conversion</h2>
                        <p class="mt-1 text-sm text-slate-600">Stage counts and drop-off between stages.</p>
                    </div>
                    <select wire:model.live="funnelFilter" class="rounded-lg border-slate-300 text-sm font-semibold text-[#0B1F3A]">
                        <option value="insurance">Insurance Funnel</option>
                        <option value="recruiting">Recruiting Funnel</option>
                    </select>
                </div>
                <div class="mt-6 space-y-4">
                    @forelse ($funnelConversion['stages'] as $stage)
                        @php($width = $funnelConversion['max_count'] > 0 ? round(($stage['count'] / $funnelConversion['max_count']) * 100) : 0)
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-semibold text-[#0B1F3A]">{{ $stage['name'] }}</span>
                                <span class="text-slate-600">{{ $stage['count'] }} @if($stage['drop_off'] > 0)<span class="text-red-600">(-{{ $stage['drop_off'] }}%)</span>@endif</span>
                            </div>
                            <div class="h-3 rounded-full bg-slate-100">
                                <div class="h-3 rounded-full bg-[#C8A24A]" style="width: {{ max(2, $width) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No funnel stages configured.</p>
                    @endforelse
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-lg border border-[#C8A24A]/30 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Lead Sources</h2>
                    <p class="mt-1 text-sm text-slate-600">Prospects by acquisition source.</p>
                    <div class="mt-5 space-y-3">
                        @forelse ($leadSources as $source)
                            @php($width = $sourceMax > 0 ? round(($source['count'] / $sourceMax) * 100) : 0)
                            <div>
                                <div class="mb-1 flex justify-between text-sm">
                                    <span class="font-medium text-[#0B1F3A]">{{ $source['source'] }}</span>
                                    <span class="text-slate-600">{{ $source['count'] }}</span>
                                </div>
                                <div class="h-2.5 rounded-full bg-slate-100">
                                    <div class="h-2.5 rounded-full bg-[#0B1F3A]" style="width: {{ max(2, $width) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">No lead source data yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-lg border border-[#C8A24A]/30 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Monthly Activity</h2>
                    <p class="mt-1 text-sm text-slate-600">Communications, activities, and appointments.</p>
                    <div class="mt-5 flex items-end gap-2" style="min-height: 10rem;">
                        @foreach ($activityTrend as $month)
                            @php($height = $activityMax > 0 ? round(($month['total'] / $activityMax) * 100) : 0)
                            <div class="flex min-w-0 flex-1 flex-col items-center gap-1">
                                <span class="text-[0.65rem] font-semibold text-[#0B1F3A]">{{ $month['total'] }}</span>
                                <div class="flex w-full flex-col justify-end rounded-t bg-slate-100" style="height: 7rem;">
                                    <div class="w-full rounded-t bg-[#C8A24A]" style="height: {{ max(4, $height) }}%"></div>
                                </div>
                                <span class="truncate text-[0.6rem] text-slate-500">{{ $month['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-lg border border-[#C8A24A]/30 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Prospect Growth</h2>
                    <p class="mt-1 text-sm text-slate-600">Cumulative new prospects over six months.</p>
                    <div class="mt-5 space-y-3">
                        @foreach ($prospectGrowth as $month)
                            @php($width = $growthMax > 0 ? round(($month['cumulative'] / $growthMax) * 100) : 0)
                            <div>
                                <div class="mb-1 flex justify-between text-sm">
                                    <span class="font-medium text-[#0B1F3A]">{{ $month['label'] }}</span>
                                    <span class="text-slate-600">+{{ $month['new_count'] }} ({{ $month['cumulative'] }} total)</span>
                                </div>
                                <div class="h-2.5 rounded-full bg-slate-100">
                                    <div class="h-2.5 rounded-full bg-[#8A6A1F]" style="width: {{ max(2, $width) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-lg border border-[#C8A24A]/30 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Dual Pipeline</h2>
                    <p class="mt-1 text-sm text-slate-600">Insurance vs recruiting by stage.</p>
                    <div class="mt-5 grid gap-6 md:grid-cols-2">
                        @foreach (['insurance' => 'Insurance', 'recruiting' => 'Recruiting'] as $key => $label)
                            <div>
                                <p class="mb-3 text-xs font-bold uppercase tracking-wide text-[#C8A24A]">{{ $label }}</p>
                                <div class="space-y-2">
                                    @foreach (collect($dualPipeline[$key])->take(6) as $stage)
                                        @php($width = $dualPipeline['max_count'] > 0 ? round(($stage['count'] / $dualPipeline['max_count']) * 100) : 0)
                                        <div>
                                            <div class="mb-0.5 flex justify-between text-xs">
                                                <span class="truncate pr-2 text-[#0B1F3A]">{{ $stage['stage'] }}</span>
                                                <span class="text-slate-500">{{ $stage['count'] }}</span>
                                            </div>
                                            <div class="h-2 rounded-full bg-slate-100">
                                                <div @class([
                                                    'h-2 rounded-full',
                                                    'bg-[#C8A24A]' => $key === 'insurance',
                                                    'bg-[#0B1F3A]' => $key === 'recruiting',
                                                ]) style="width: {{ max(2, $width) }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <aside>
            <livewire:prospects.prospect-goals-panel />
        </aside>
    </div>
</div>
