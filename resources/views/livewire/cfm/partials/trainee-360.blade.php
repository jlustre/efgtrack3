@php
    $p = $trainee360['profile'];
    $progress = $trainee360['progress'];
    $risk = $trainee360['risk'];
@endphp

@include('dashboard.partials.stat-card-themes')
@include('livewire.cfm.partials.stat-card-themes')

<div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex items-start gap-4">
            @if ($p['photo_url'])
                <img src="{{ $p['photo_url'] }}" alt="" class="h-16 w-16 rounded-full object-cover ring-4 ring-[#FFF9EA]">
            @else
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-[#0B1F3A] text-lg font-bold text-[#C8A24A]">{{ $p['initials'] }}</span>
            @endif

            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Trainee 360°</p>
                <h2 class="mt-1 text-2xl font-semibold text-[#0B1F3A]">{{ $p['name'] }}</h2>
                <p class="mt-1 text-sm text-slate-600">{{ $p['rank'] }} · {{ $p['rank_name'] }} · Joined {{ $p['joined_at'] }}</p>
                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-700">FAP: {{ $p['fap_status'] }}</span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-700">Licensing: {{ $p['licensing_status'] }}</span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-700">{{ $p['location'] }}</span>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach ($trainee360['quick_actions'] as $action)
                <button
                    type="button"
                    wire:click="openTraineeQuickActionModal(@js($action['action']))"
                    @class([
                        'inline-flex items-center rounded-lg px-3 py-2 text-sm font-semibold transition',
                        'bg-[#C8A24A] text-[#0B1F3A] hover:bg-[#D8B75F]' => ($action['style'] ?? '') === 'primary',
                        'border border-slate-200 text-[#0B1F3A] hover:bg-slate-50' => ($action['style'] ?? '') !== 'primary',
                    ])
                >
                    {{ $action['label'] }}
                </button>
            @endforeach

            @if ($trainee360['checklist_links']['fap'])
                <button
                    type="button"
                    @click="openTraineeChecklistModal(@js($trainee360['checklist_links']['fap']))"
                    class="inline-flex items-center rounded-lg border border-[#C8A24A]/40 px-3 py-2 text-sm font-semibold text-[#8A6A1F] transition hover:bg-[#FFF9EA]"
                >
                    Mentoring checklist
                </button>
            @endif
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 border-t border-slate-200 pt-6 sm:grid-cols-2 lg:grid-cols-4">
        <div><span class="text-xs uppercase text-slate-500">Sponsor</span><p class="mt-1 text-sm font-medium text-[#0B1F3A]">{{ $p['sponsor'] }}</p></div>
        <div><span class="text-xs uppercase text-slate-500">Agency Owner</span><p class="mt-1 text-sm font-medium text-[#0B1F3A]">{{ $p['agency_owner'] }}</p></div>
        <div><span class="text-xs uppercase text-slate-500">CFM</span><p class="mt-1 text-sm font-medium text-[#0B1F3A]">{{ $p['cfm'] }}</p></div>
        <div><span class="text-xs uppercase text-slate-500">Contact</span><p class="mt-1 text-sm font-medium text-[#0B1F3A]">{{ $p['email'] }} · {{ $p['phone'] }}</p></div>
    </div>
</div>

<div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
    @foreach ([
        'onboarding' => 'Onboarding',
        'licensing' => 'Licensing',
        'fap' => 'FAP',
        'training' => 'Training',
        'rank' => 'Rank',
    ] as $key => $label)
        @php($theme = cfmPortalStatCardTheme($key))
        <div @class([$theme['card']])>
            <p @class([$theme['label']])>{{ $label }}</p>
            <p @class(['mt-2 text-xl font-bold text-[#0B1F3A]'])>{{ $progress[$key] ?? 0 }}%</p>
            <div @class(['mt-2 h-1.5 w-full rounded-full', $theme['bar_track']])>
                <div @class(['h-1.5 rounded-full', $theme['bar_fill']]) style="width: {{ min(100, (int) ($progress[$key] ?? 0)) }}%"></div>
            </div>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Recruiting</h3>
        <dl class="mt-4 space-y-3 text-sm">
            <div class="flex justify-between"><dt class="text-slate-600">Direct recruits</dt><dd class="font-semibold text-[#0B1F3A]">{{ $trainee360['recruiting']['direct_recruits'] }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-600">Total downline</dt><dd class="font-semibold text-[#0B1F3A]">{{ $trainee360['recruiting']['total_downline'] }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-600">Prospects</dt><dd class="font-semibold text-[#0B1F3A]">{{ $trainee360['recruiting']['prospects'] }}</dd></div>
        </dl>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Goals & Performance</h3>
            <a href="{{ route('goals.coaching') }}" class="text-xs font-semibold text-[#8A6A1F] hover:text-[#C8A24A]">Open coaching →</a>
        </div>

        @if (count($trainee360['goals']) === 0)
            <p class="text-sm text-slate-500">No active goals yet.</p>
        @else
            <ul class="space-y-3">
                @foreach ($trainee360['goals'] as $goal)
                    <li class="rounded-lg bg-slate-50 px-4 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-[#0B1F3A]">{{ $goal['name'] }}</p>
                                <p class="text-xs text-slate-500">{{ $goal['category'] }} · Due {{ $goal['deadline'] ?? '—' }}</p>
                            </div>
                            <span class="text-sm font-bold text-[#8A6A1F]">{{ $goal['progress'] }}%</span>
                        </div>
                        <div class="mt-2 h-1.5 w-full rounded-full bg-slate-200">
                            <div class="h-1.5 rounded-full bg-[#C8A24A]" style="width: {{ min(100, (int) $goal['progress']) }}%"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <div @class([
        'rounded-xl border p-5 shadow-sm',
        'border-red-200 bg-red-50' => $risk['level'] === 'high',
        'border-amber-200 bg-amber-50' => $risk['level'] === 'medium',
        'border-emerald-200 bg-emerald-50' => $risk['level'] === 'low',
    ])>
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-[#0B1F3A]">Risk Assessment</h3>
            <span class="rounded-full bg-white/70 px-3 py-1 text-xs font-bold text-[#0B1F3A]">Score {{ $risk['score'] }}/100</span>
        </div>

        @if (count($risk['flags']) === 0)
            <p class="mt-4 text-sm text-emerald-800">No risk flags detected.</p>
        @else
            <ul class="mt-4 list-inside list-disc space-y-1 text-sm text-[#0B1F3A]">
                @foreach ($risk['flags'] as $flag)
                    <li>{{ $flag }}</li>
                @endforeach
            </ul>
        @endif

        <div class="mt-4 border-t border-black/5 pt-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Recommended actions</p>
            <ul class="mt-2 space-y-1 text-sm text-slate-700">
                @foreach ($risk['recommended_actions'] as $action)
                    <li>{{ $action }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Coaching Suggestions</h3>
        @if (count($trainee360['coaching_suggestions']) === 0)
            <p class="mt-4 text-sm text-slate-500">No coaching suggestions at this time.</p>
        @else
            <ul class="mt-4 space-y-2">
                @foreach ($trainee360['coaching_suggestions'] as $suggestion)
                    <li class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2 text-sm text-slate-700">{{ $suggestion }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

<div class="rounded-xl border border-dashed border-[#C8A24A]/40 bg-[#FFF9EA]/50 p-5">
    <h3 class="text-sm font-semibold text-[#0B1F3A]">Tracker links</h3>
    <div class="mt-3 flex flex-wrap gap-2">
        <a href="{{ $trainee360['checklist_links']['onboarding'] }}" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-[#0B1F3A] hover:border-[#C8A24A]">Onboarding tracker</a>
        <a href="{{ $trainee360['checklist_links']['licensing'] }}" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-[#0B1F3A] hover:border-[#C8A24A]">Licensing tracker</a>
        @if ($trainee360['checklist_links']['fap'])
            <a href="{{ $trainee360['checklist_links']['fap'] }}" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-[#0B1F3A] hover:border-[#C8A24A]">Mentoring checklist</a>
        @endif
    </div>
</div>
