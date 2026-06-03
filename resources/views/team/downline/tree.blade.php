<x-app-layout>
    <section class="space-y-6" x-data="{ zoom: 1, compact: false }">
        <div class="rounded-lg border border-slate-700 bg-gradient-to-br from-[#05070B] via-[#07111F] to-[#1B2433] p-6 text-white shadow-sm">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Genealogy View</p>
                    <h1 class="mt-2 text-2xl font-semibold">Sponsor Tree</h1>
                    <p class="mt-2 text-sm font-medium leading-6 text-slate-100">Closure-table powered hierarchy with expandable branches, rank badges, progress, and member actions.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" x-on:click="zoom = Math.max(.7, zoom - .1)" class="rounded-lg border border-white/30 bg-white/10 px-3 py-2 text-sm font-semibold text-white hover:bg-white/20">Zoom Out</button>
                    <button type="button" x-on:click="zoom = Math.min(1.4, zoom + .1)" class="rounded-lg border border-white/30 bg-white/10 px-3 py-2 text-sm font-semibold text-white hover:bg-white/20">Zoom In</button>
                    <button type="button" x-on:click="compact = ! compact" class="rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-3 py-2 text-sm font-semibold text-[#0B1F3A]">Compact</button>
                    <a href="{{ route('team.hierarchy') }}" class="rounded-lg border border-white/30 bg-white/10 px-3 py-2 text-sm font-semibold text-white hover:bg-white/20">Hierarchy Table</a>
                    <a href="{{ route('team.table') }}" class="rounded-lg border border-white/30 bg-white/10 px-3 py-2 text-sm font-semibold text-white hover:bg-white/20">Flat Table</a>
                </div>
            </div>
        </div>

        <form method="GET" class="grid gap-3 rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-4 shadow-sm md:grid-cols-5">
            <input name="search" value="{{ request('search') }}" placeholder="Search and jump to member..." class="rounded-lg border-slate-300 text-sm md:col-span-2">
            <select name="rank_id" class="rounded-lg border-slate-300 text-sm">
                <option value="">All Ranks</option>
                @foreach ($filters['ranks'] as $rank)
                    <option value="{{ $rank->id }}" @selected((string) request('rank_id') === (string) $rank->id)>{{ $rank->code }} - {{ $rank->name }}</option>
                @endforeach
            </select>
            <select name="country" class="rounded-lg border-slate-300 text-sm">
                <option value="">All Countries</option>
                @foreach ($filters['countries'] as $country)
                    <option value="{{ $country }}" @selected(request('country') === $country)>{{ $country }}</option>
                @endforeach
            </select>
            <button class="rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white">Apply Filters</button>
        </form>

        <div class="min-h-[calc(100vh-21rem)] overflow-auto rounded-lg border border-slate-400 bg-[#05070B] shadow-sm">
            <div
                class="inline-block min-w-full px-12 py-6 transition"
                :style="`transform: scale(${zoom}); transform-origin: top left;`"
            >
                <div class="mx-auto flex w-max flex-col items-center">
                    @include('team.downline.partials.tree-node', ['node' => $root, 'isRoot' => true])

                    @if ($children->isNotEmpty())
                        <div class="h-8 w-px shrink-0 bg-[#C8A24A] {{ $children->count() > 1 ? '-mb-px' : '' }}" aria-hidden="true"></div>

                        <div class="relative inline-flex items-start justify-center gap-5">
                            @if ($children->count() > 1)
                                <div
                                    class="pointer-events-none absolute top-0 h-px bg-[#C8A24A]"
                                    style="left: 7rem; width: calc(100% - 14rem);"
                                    aria-hidden="true"
                                ></div>
                            @endif

                            @foreach ($children as $child)
                                <div x-data="{ expanded: false }" class="flex w-56 shrink-0 flex-col items-center">
                                    <div class="h-8 w-px shrink-0 bg-[#C8A24A] {{ $children->count() > 1 ? '-mb-px' : '' }}" aria-hidden="true"></div>
                                    <div class="relative w-full shrink-0">
                                        @include('team.downline.partials.tree-node', ['node' => $child, 'isRoot' => false])
                                        <button type="button" x-on:click="expanded = ! expanded" class="mt-3 w-full rounded-lg border border-[#C8A24A]/60 bg-white/10 px-3 py-2 text-xs font-bold uppercase tracking-wide text-[#C8A24A] hover:bg-white/15">
                                            Branch Actions
                                        </button>
                                        <div x-show="expanded" x-transition class="mt-2 grid gap-2 rounded-lg border border-[#C8A24A]/40 bg-[#F8FAFC] p-3 text-sm font-semibold text-[#0B1F3A] shadow-lg">
                                            <a href="{{ route('team.member', $child['id']) }}" class="rounded-md px-2 py-1 transition hover:bg-[#FFF4CF] hover:text-[#8A6A1F]">View Profile</a>
                                            <a href="{{ route('team.member.tree', $child['id']) }}" class="rounded-md px-2 py-1 transition hover:bg-[#FFF4CF] hover:text-[#8A6A1F]">View Direct Recruits</a>
                                            <a href="{{ route('licensing.index') }}" class="rounded-md px-2 py-1 transition hover:bg-[#FFF4CF] hover:text-[#8A6A1F]">View Licensing Progress</a>
                                            <a href="{{ route('training.index') }}" class="rounded-md px-2 py-1 transition hover:bg-[#FFF4CF] hover:text-[#8A6A1F]">View Training Progress</a>
                                            <a href="{{ route('team.table', ['search' => $child['name']]) }}" class="rounded-md px-2 py-1 transition hover:bg-[#FFF4CF] hover:text-[#8A6A1F]">Open In Table View</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-8 rounded-lg border border-white/10 bg-white/5 px-6 py-4 text-center text-slate-300">No direct recruits under this root yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
