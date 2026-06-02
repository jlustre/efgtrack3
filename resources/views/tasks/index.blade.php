<x-app-layout>
    <section x-data="{ tab: 'all' }" class="space-y-6">
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-[#F8F3E7] shadow-sm">
            <div class="bg-[#0B1F3A] px-6 py-6 text-white">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">My Tasks</p>
                        <h1 class="mt-2 text-2xl font-semibold">Today's action center</h1>
                        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-200">
                            Your assigned confirmations, mentor assignments, email follow-ups, and advancement reviews in one place.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
                        <div class="rounded-md bg-white/10 px-4 py-3">
                            <div class="text-xs uppercase tracking-wide text-slate-300">Date</div>
                            <div class="mt-1 font-semibold">{{ $todayLabel }}</div>
                        </div>
                        <div class="rounded-md bg-white/10 px-4 py-3">
                            <div class="text-xs uppercase tracking-wide text-slate-300">Role</div>
                            <div class="mt-1 font-semibold">{{ $user->roles->pluck('name')->first() ?? 'member' }}</div>
                        </div>
                        <div class="rounded-md bg-white/10 px-4 py-3">
                            <div class="text-xs uppercase tracking-wide text-slate-300">Team</div>
                            <div class="mt-1 font-semibold">{{ $user->team?->name ?? 'Unassigned' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 p-6 md:grid-cols-2 xl:grid-cols-6">
                <button type="button" x-on:click="tab = 'all'" class="rounded-lg border border-[#C8A24A]/25 bg-[#FFF9EA] p-5 text-left shadow-sm transition hover:border-[#C8A24A]">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-slate-600">Total Tasks</p>
                        <span class="rounded-full bg-[#C8A24A]/15 px-2.5 py-1 text-xs font-bold text-[#8A6A1F]">{{ $stats['total'] }}</span>
                    </div>
                    <div class="mt-3 text-3xl font-semibold text-[#0B1F3A]">{{ $stats['total'] }}</div>
                </button>

                <button type="button" x-on:click="tab = 'confirmations'" class="rounded-lg border border-emerald-100 bg-emerald-50/70 p-5 text-left shadow-sm transition hover:border-[#C8A24A]">
                    <p class="text-sm font-semibold text-slate-600">Confirmations</p>
                    <div class="mt-3 text-3xl font-semibold text-[#0B1F3A]">{{ $stats['confirmations'] }}</div>
                </button>

                <button type="button" x-on:click="tab = 'cfm'" class="rounded-lg border border-indigo-100 bg-indigo-50/70 p-5 text-left shadow-sm transition hover:border-[#C8A24A]">
                    <p class="text-sm font-semibold text-slate-600">CFM Assignment</p>
                    <div class="mt-3 text-3xl font-semibold text-[#0B1F3A]">{{ $stats['cfm_assignments'] }}</div>
                </button>

                <button type="button" x-on:click="tab = 'emails'" class="rounded-lg border border-sky-100 bg-sky-50/70 p-5 text-left shadow-sm transition hover:border-[#C8A24A]">
                    <p class="text-sm font-semibold text-slate-600">Emails</p>
                    <div class="mt-3 text-3xl font-semibold text-[#0B1F3A]">{{ $stats['emails'] }}</div>
                </button>

                <button type="button" x-on:click="tab = 'promotions'" class="rounded-lg border border-purple-100 bg-purple-50/70 p-5 text-left shadow-sm transition hover:border-[#C8A24A]">
                    <p class="text-sm font-semibold text-slate-600">Promotion Review</p>
                    <div class="mt-3 text-3xl font-semibold text-[#0B1F3A]">{{ $stats['promotions'] }}</div>
                </button>

                <div class="rounded-lg border border-red-100 bg-red-50 p-5">
                    <p class="text-sm font-semibold text-red-700">High Priority</p>
                    <div class="mt-3 text-3xl font-semibold text-red-800">{{ $stats['high_priority'] }}</div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1fr_22rem]">
            <div class="space-y-6">
                <div x-show="tab === 'all'" x-transition class="overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-[#F8F3E7] shadow-sm">
                    <div class="flex flex-col gap-2 border-b border-slate-200 bg-white/70 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-[#0B1F3A]">Priority Queue</h2>
                            <p class="mt-1 text-sm text-slate-600">Ordered by urgency and age.</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $allTasks->count() }} open</span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse ($allTasks as $task)
                            @include('tasks.partials.task-row', ['task' => $task])
                        @empty
                            @include('tasks.partials.empty-state', ['title' => 'No open tasks', 'message' => 'You are clear right now.'])
                        @endforelse
                    </div>
                </div>

                @foreach ($groupedTasks as $groupName => $tasks)
                    @php
                        $tabName = match ($groupName) {
                            'Confirmations' => 'confirmations',
                            'CFM Assignment' => 'cfm',
                            'Email Follow-Up' => 'emails',
                            'Promotion Review' => 'promotions',
                        };
                    @endphp

                    <div x-show="tab === @js($tabName)" x-transition class="overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-[#F8F3E7] shadow-sm">
                        <div class="flex flex-col gap-2 border-b border-slate-200 bg-white/70 px-6 py-5 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-[#0B1F3A]">{{ $groupName }}</h2>
                                <p class="mt-1 text-sm text-slate-600">{{ $tasks->count() }} open {{ str($groupName)->lower() }} {{ str($tasks->count() === 1 ? 'task' : 'tasks') }}</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $tasks->count() }} open</span>
                        </div>

                        <div class="divide-y divide-slate-100">
                            @forelse ($tasks as $task)
                                @include('tasks.partials.task-row', ['task' => $task])
                            @empty
                                @include('tasks.partials.empty-state', ['title' => 'Nothing open here', 'message' => 'This queue is clear.'])
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>

            <aside class="space-y-6">
                <div class="rounded-lg border border-slate-200 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-5 shadow-sm">
                    <h2 class="text-base font-semibold text-[#0B1F3A]">Queue Mix</h2>
                    <div class="mt-5 space-y-4">
                        @foreach ([
                            ['label' => 'Confirmations', 'value' => $stats['confirmations'], 'color' => 'bg-emerald-500'],
                            ['label' => 'CFM Assignment', 'value' => $stats['cfm_assignments'], 'color' => 'bg-indigo-500'],
                            ['label' => 'Emails', 'value' => $stats['emails'], 'color' => 'bg-sky-500'],
                            ['label' => 'Promotion Review', 'value' => $stats['promotions'], 'color' => 'bg-purple-500'],
                        ] as $item)
                            @php($percent = $stats['total'] > 0 ? (int) round(($item['value'] / $stats['total']) * 100) : 0)
                            <div>
                                <div class="mb-2 flex items-center justify-between text-sm">
                                    <span class="font-semibold text-slate-700">{{ $item['label'] }}</span>
                                    <span class="font-semibold text-[#0B1F3A]">{{ $item['value'] }}</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full {{ $item['color'] }}" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-lg border border-slate-200 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-5 shadow-sm">
                    <h2 class="text-base font-semibold text-[#0B1F3A]">Fast Actions</h2>
                    <div class="mt-4 grid gap-2">
                        @foreach ($fastActions as $action)
                            <a href="{{ $action['url'] }}" class="group grid grid-cols-[auto_1fr_auto] items-center gap-3 rounded-md border border-slate-200 px-3 py-3 text-sm transition hover:border-[#C8A24A] hover:bg-[#C8A24A]/5">
                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition group-hover:bg-[#0B1F3A] group-hover:text-white">
                                    @include('tasks.partials.fast-action-icon', ['icon' => $action['icon']])
                                </span>
                                <span class="min-w-0">
                                    <span class="block font-semibold text-[#0B1F3A]">{{ $action['label'] }}</span>
                                    <span class="mt-0.5 block text-xs leading-5 text-slate-500">{{ $action['description'] }}</span>
                                </span>
                                @if (! is_null($action['count']))
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-700">{{ $action['count'] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </section>
</x-app-layout>
