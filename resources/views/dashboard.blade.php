<x-app-layout>
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Private Team Portal</p>
            <h1 class="text-2xl font-semibold text-[#0B1F3A]">Dashboard</h1>
        </div>

        <div class="rounded-full border border-[#C8A24A]/40 bg-white px-4 py-2 text-sm font-semibold text-[#0B1F3A] shadow-sm">
            Current Rank: {{ auth()->user()?->rank?->code ?? 'New Recruit' }}
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Onboarding', 'value' => '65%', 'bar' => '65'],
            ['label' => 'Licensing', 'value' => '40%', 'bar' => '40'],
            ['label' => 'Apprenticeship', 'value' => '25%', 'bar' => '25'],
            ['label' => 'Training', 'value' => '70%', 'bar' => '70'],
        ] as $card)
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-600">{{ $card['label'] }}</h2>
                    <span class="text-lg font-bold text-[#0B1F3A]">{{ $card['value'] }}</span>
                </div>
                <div class="mt-4 h-2 rounded-full bg-slate-100">
                    <div class="h-2 rounded-full bg-[#C8A24A]" style="width: {{ $card['bar'] }}%"></div>
                </div>
            </section>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-[#0B1F3A]">Next Rank Requirements</h2>
                <span class="rounded-full bg-[#0B1F3A] px-3 py-1 text-xs font-semibold text-white">SFA Track</span>
            </div>

            <div class="space-y-3">
                @foreach (['Complete onboarding checklist', 'Finish licensing milestones', 'Complete core training', 'Mentor review submitted'] as $item)
                    <div class="flex items-center justify-between rounded-md border border-slate-100 bg-slate-50 px-4 py-3">
                        <span class="text-sm font-medium">{{ $item }}</span>
                        <span class="text-xs font-semibold uppercase text-[#C8A24A]">In progress</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-[#0B1F3A]">Assigned CFM</h2>
            <div class="rounded-md bg-[#0B1F3A] p-4 text-white">
                <div class="text-sm text-slate-300">Certified Field Mentor</div>
                <div class="mt-1 text-xl font-semibold">Unassigned</div>
                <div class="mt-4 h-1.5 rounded-full bg-white/20">
                    <div class="h-1.5 w-1/4 rounded-full bg-[#C8A24A]"></div>
                </div>
            </div>
        </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Team Communications</p>
                    <h2 class="mt-1 text-lg font-semibold text-[#0B1F3A]">Latest Announcements</h2>
                </div>
                <a href="{{ route('announcements.index') }}" class="rounded-full border border-[#C8A24A]/50 px-3 py-1 text-xs font-semibold text-[#0B1F3A] transition hover:bg-[#C8A24A]/10">
                    View All
                </a>
            </div>

            <div class="space-y-3">
                @foreach ([
                    ['title' => 'Weekly leadership call reminder', 'meta' => 'Posted today', 'badge' => 'Priority'],
                    ['title' => 'New onboarding checklist updates', 'meta' => 'Posted 2 days ago', 'badge' => 'Training'],
                    ['title' => 'Recognition wall submissions now open', 'meta' => 'Posted this week', 'badge' => 'Culture'],
                ] as $announcement)
                    <div class="rounded-md border border-slate-100 bg-slate-50 px-4 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="text-sm font-semibold text-[#0B1F3A]">{{ $announcement['title'] }}</h3>
                            <span class="shrink-0 rounded-full bg-[#0B1F3A] px-2 py-1 text-[0.68rem] font-semibold text-white">{{ $announcement['badge'] }}</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ $announcement['meta'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Calendar</p>
                    <h2 class="mt-1 text-lg font-semibold text-[#0B1F3A]">Upcoming Events</h2>
                </div>
                <a href="{{ route('events.index') }}" class="rounded-full border border-[#C8A24A]/50 px-3 py-1 text-xs font-semibold text-[#0B1F3A] transition hover:bg-[#C8A24A]/10">
                    View Calendar
                </a>
            </div>

            <div class="space-y-3">
                @foreach ([
                    ['date' => 'Jun 04', 'title' => 'Licensing study session', 'time' => '6:00 PM PT'],
                    ['date' => 'Jun 08', 'title' => 'Field apprenticeship workshop', 'time' => '10:00 AM PT'],
                    ['date' => 'Jun 12', 'title' => 'Elite Financial Growth leadership huddle', 'time' => '7:00 PM PT'],
                ] as $event)
                    <div class="flex gap-4 rounded-md border border-slate-100 bg-slate-50 px-4 py-3">
                        <div class="flex h-12 w-16 shrink-0 items-center justify-center rounded-md bg-[#0B1F3A] text-center text-xs font-bold uppercase leading-tight text-[#C8A24A]">
                            {{ $event['date'] }}
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-[#0B1F3A]">{{ $event['title'] }}</h3>
                            <p class="mt-1 text-xs text-slate-500">{{ $event['time'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
