<div class="relative w-56 rounded-lg border {{ $isRoot ? 'border-[#C8A24A]' : 'border-slate-700' }} bg-[#07111F] p-3 text-white shadow-lg">
    <div class="group relative flex items-center gap-3">
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-[#C8A24A] bg-[#FFF4CF] text-sm font-bold text-[#0B1F3A]">
            {{ $node['avatar'] }}
        </div>
        <div class="min-w-0 flex-1">
            <div>
                <a href="{{ route('team.member', $node['id']) }}" class="truncate text-sm font-semibold text-white hover:text-[#C8A24A]">{{ $node['name'] }}</a>
            </div>
            <div class="mt-1 flex items-center gap-2">
                @include('team.downline.partials.member-badge', ['member' => $node])
                <span
                    class="inline-flex h-6 min-w-8 items-center justify-center rounded-full border border-white/20 bg-white/10 px-2 text-[10px] font-bold uppercase tracking-wide text-white"
                    title="{{ $node['country'] }}"
                >
                    {{ $node['country_flag'] }}
                </span>
                @if (! $isRoot)
                    <a
                        href="{{ route('team.member.tree', $node['id']) }}"
                        title="Make this member the top card"
                        class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-[#C8A24A]/70 bg-[#101B2B] text-[#C8A24A] transition hover:bg-[#C8A24A] hover:text-[#0B1F3A]"
                    >
                        <span class="sr-only">Make this member the top card</span>
                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 3.25a.75.75 0 0 1 .53.22l4.25 4.25a.75.75 0 0 1-1.06 1.06L10.75 5.81V16a.75.75 0 0 1-1.5 0V5.81L6.28 8.78a.75.75 0 0 1-1.06-1.06l4.25-4.25a.75.75 0 0 1 .53-.22Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @elseif ($node['upline_tree_url'])
                    <a
                        href="{{ $node['upline_tree_url'] }}"
                        title="Show direct upline"
                        class="inline-flex h-6 w-6 items-center justify-center rounded-full border border-[#C8A24A]/70 bg-[#101B2B] text-[#C8A24A] transition hover:bg-[#C8A24A] hover:text-[#0B1F3A]"
                    >
                        <span class="sr-only">Show direct upline</span>
                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 3.25a.75.75 0 0 1 .53.22l4.25 4.25a.75.75 0 0 1-1.06 1.06L10.75 5.81V16a.75.75 0 0 1-1.5 0V5.81L6.28 8.78a.75.75 0 0 1-1.06-1.06l4.25-4.25a.75.75 0 0 1 .53-.22Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endif
            </div>
        </div>

        <div class="pointer-events-none absolute left-0 top-full z-30 mt-3 w-80 rounded-lg border border-[#C8A24A]/70 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-4 text-left text-xs text-slate-700 opacity-0 shadow-2xl ring-1 ring-[#C8A24A]/20 transition group-hover:opacity-100 group-focus-within:opacity-100">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-[#0B1F3A]">{{ $node['name'] }}</div>
                    <div class="mt-1 flex flex-wrap items-center gap-2 text-slate-600">
                        <span class="inline-flex h-5 min-w-7 items-center justify-center rounded-full border border-[#C8A24A]/50 bg-[#FFF4CF] px-2 text-[10px] font-bold text-[#0B1F3A]">{{ $node['country_flag'] }}</span>
                        <span>{{ $node['country'] }}</span>
                        <span>&middot;</span>
                        <span>{{ $node['city'] }}</span>
                        <span>&middot;</span>
                        <span>{{ $node['timezone'] }}</span>
                    </div>
                </div>
                @include('team.downline.partials.member-badge', ['member' => $node])
            </div>

            <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                <div class="rounded-md border border-[#C8A24A]/50 bg-white p-2">
                    <div class="font-bold text-[#8A6A1F]">{{ $node['metrics']['direct_recruits'] }}</div>
                    <div class="font-semibold text-[#0B1F3A]">Direct</div>
                </div>
                <div class="rounded-md border border-[#C8A24A]/50 bg-white p-2">
                    <div class="font-bold text-[#8A6A1F]">{{ $node['metrics']['total_downline'] }}</div>
                    <div class="font-semibold text-[#0B1F3A]">Team</div>
                </div>
                <div class="rounded-md border border-[#C8A24A]/50 bg-white p-2">
                    <div class="font-bold text-[#8A6A1F]">{{ $node['metrics']['prospects'] }}</div>
                    <div class="font-semibold text-[#0B1F3A]">Prospects</div>
                </div>
            </div>

            <div class="mt-4 space-y-2">
                <div class="flex items-center justify-between"><span>Status</span><span class="font-semibold text-[#0B1F3A]">{{ $node['status'] }}</span></div>
                <div class="flex items-center justify-between"><span>Sponsor</span><span class="font-semibold text-[#0B1F3A]">{{ $node['sponsor'] }}</span></div>
                <div class="flex items-center justify-between"><span>CFM</span><span class="font-semibold text-[#0B1F3A]">{{ $node['mentor'] }}</span></div>
                <div class="flex items-center justify-between"><span>Joined</span><span class="font-semibold text-[#0B1F3A]">{{ $node['joined_at'] }}</span></div>
                <div class="flex items-center justify-between"><span>Last Activity</span><span class="font-semibold text-[#0B1F3A]">{{ $node['last_activity'] }}</span></div>
            </div>

            <div class="mt-4 space-y-2">
                <div class="h-2 overflow-hidden rounded-full bg-slate-200">
                    <div class="h-full rounded-full bg-[#C8A24A]" style="width: {{ $node['progress']['onboarding'] }}%"></div>
                </div>
                <div class="font-semibold text-slate-700">
                    Onboarding {{ $node['progress']['onboarding'] }}% &middot; Licensing {{ $node['progress']['licensing'] }}% &middot; Training {{ $node['progress']['training'] }}%
                </div>
            </div>
        </div>
    </div>
</div>
