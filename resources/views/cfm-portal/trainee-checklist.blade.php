<x-app-layout>
    @php
        $assignment = $payload['assignment'];
        $trainee = $payload['trainee'];
        $stats = $payload['stats'];
        $phases = $payload['phases'];
    @endphp

    <section class="cfm-management-page -mx-4 -mt-6 bg-black text-gray-200 font-sans antialiased sm:-mx-6 lg:-mx-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8 space-y-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <a href="{{ route('cfm.portal') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 transition hover:text-amber-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                        Back to CFM Portal
                    </a>
                    <p class="mt-3 text-sm font-semibold uppercase tracking-wide text-amber-400">Trainee Mentoring Checklist</p>
                    <h1 class="mt-1 text-2xl font-semibold text-white">{{ $trainee->name }}</h1>
                    <p class="mt-2 max-w-3xl text-sm text-gray-400">
                        Track mentoring progress across assignment, orientation, licensing, compliance, training, FAP, and graduation.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
                    <div class="rounded-xl border border-gray-800 bg-gray-900/60 px-4 py-3">
                        <div class="text-xs uppercase tracking-wide text-gray-500">Rank</div>
                        <div class="mt-1 font-semibold text-white">{{ $trainee->rank?->name ?? 'Associate' }}</div>
                    </div>
                    <div class="rounded-xl border border-gray-800 bg-gray-900/60 px-4 py-3">
                        <div class="text-xs uppercase tracking-wide text-gray-500">Assignment</div>
                        <div class="mt-1 font-semibold text-white">{{ ucfirst($assignment->status) }}</div>
                    </div>
                    <div class="rounded-xl border border-gray-800 bg-gray-900/60 px-4 py-3 col-span-2 sm:col-span-1">
                        <div class="text-xs uppercase tracking-wide text-gray-500">Location</div>
                        <div class="mt-1 font-semibold text-white">{{ $trainee->profile?->city ?: '—' }}{{ $trainee->profile?->province ? ', '.$trainee->profile->province : '' }}</div>
                    </div>
                </div>
            </div>

            @if (session('profile_feedback'))
                <div class="rounded-xl border px-4 py-3 text-sm font-medium {{ session('profile_feedback.type') === 'success' ? 'border-emerald-800 bg-emerald-950/50 text-emerald-300' : 'border-red-800 bg-red-950/50 text-red-300' }}">
                    {{ session('profile_feedback.message') }}
                </div>
            @endif

            <div class="grid gap-4 lg:grid-cols-3">
                <div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 p-5 lg:col-span-1">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-amber-300">Overall Progress</p>
                        <span class="rounded-full bg-amber-500/20 px-2.5 py-1 text-xs font-bold text-amber-300">{{ $stats['percent'] }}%</span>
                    </div>
                    <div class="mt-3 text-3xl font-semibold text-white">{{ $stats['completed'] }}/{{ $stats['total'] }}</div>
                    <p class="mt-1 text-sm text-gray-400">Items completed</p>
                    <div class="mt-4 h-3 overflow-hidden rounded-full bg-gray-800">
                        <div class="h-full rounded-full bg-amber-500 transition-all" style="width: {{ $stats['percent'] }}%"></div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-800 bg-gray-900/40 p-5 lg:col-span-2">
                    <h2 class="text-sm font-semibold text-gray-300">Phase Overview</h2>
                    <div class="mt-4 grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($phases as $phase)
                            <div class="rounded-xl border border-gray-800 bg-gray-900/50 px-3 py-2.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-semibold text-gray-400">Phase {{ $phase['phase_number'] }}</span>
                                    <span class="text-xs font-bold text-amber-400">{{ $phase['percent'] }}%</span>
                                </div>
                                <p class="mt-1 line-clamp-2 text-sm text-white">{{ $phase['phase_title'] }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $phase['completed'] }}/{{ $phase['total'] }} complete</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                @foreach ($phases as $phase)
                    <div
                        x-data="{ open: {{ $phase['percent'] < 100 ? 'true' : 'false' }} }"
                        class="overflow-hidden rounded-2xl border border-gray-800 bg-gray-900/40"
                    >
                        <button
                            type="button"
                            x-on:click="open = ! open"
                            class="flex w-full items-start justify-between gap-4 px-6 py-5 text-left transition hover:bg-gray-800/40"
                            :aria-expanded="open.toString()"
                        >
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-amber-500/15 px-2.5 py-0.5 text-xs font-bold text-amber-400">Phase {{ $phase['phase_number'] }}</span>
                                    <span class="text-xs font-semibold text-gray-500">{{ $phase['completed'] }}/{{ $phase['total'] }}</span>
                                </div>
                                <h2 class="mt-2 text-lg font-semibold text-white">{{ $phase['phase_title'] }}</h2>
                                @if ($phase['phase_target'])
                                    <p class="mt-1 text-sm text-gray-500">Target: {{ $phase['phase_target'] }}</p>
                                @endif
                            </div>
                            <div class="flex shrink-0 items-center gap-3">
                                <div class="hidden w-28 sm:block">
                                    <div class="h-2 overflow-hidden rounded-full bg-gray-800">
                                        <div class="h-full rounded-full bg-amber-500" style="width: {{ $phase['percent'] }}%"></div>
                                    </div>
                                </div>
                                <svg class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </div>
                        </button>

                        <div x-show="open" x-cloak class="border-t border-gray-800">
                            @foreach ($phase['sections'] as $section)
                                <div class="border-b border-gray-800/70 last:border-b-0">
                                    <div class="bg-gray-900/60 px-6 py-3">
                                        <h3 class="text-sm font-semibold text-amber-300/90">{{ $section['title'] }}</h3>
                                    </div>
                                    <div class="divide-y divide-gray-800/70">
                                        @foreach ($section['items'] as $item)
                                            <div class="grid gap-4 px-6 py-4 lg:grid-cols-[auto_1fr_auto] lg:items-start">
                                                <form
                                                    method="POST"
                                                    action="{{ route('cfm.portal.trainees.checklist.update', [$assignment, $item['id']]) }}"
                                                    class="pt-0.5"
                                                >
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="completed" value="0">
                                                    <input
                                                        type="checkbox"
                                                        name="completed"
                                                        value="1"
                                                        class="h-5 w-5 rounded border-2 border-gray-600 bg-gray-900 text-amber-500 focus:ring-amber-500 focus:ring-offset-gray-900"
                                                        @checked($item['is_completed'])
                                                        onchange="this.form.submit()"
                                                        aria-label="Mark {{ $item['title'] }} as complete"
                                                    >
                                                </form>

                                                <div class="min-w-0">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <p class="font-medium {{ $item['is_completed'] ? 'text-gray-500 line-through' : 'text-white' }}">
                                                            {{ $item['title'] }}
                                                        </p>
                                                        @if ($item['is_required'])
                                                            <span class="rounded-full bg-amber-500/10 px-2 py-0.5 text-xs font-bold text-amber-400">Required</span>
                                                        @endif
                                                    </div>
                                                    @if ($item['notes'])
                                                        <p class="mt-1 text-sm text-gray-500">{{ $item['notes'] }}</p>
                                                    @endif
                                                </div>

                                                <div class="lg:text-right">
                                                    @if (! empty($item['action_url']))
                                                        <a href="{{ $item['action_url'] }}"
                                                            class="mb-2 inline-flex items-center justify-center rounded-lg border border-amber-500/40 bg-amber-500/10 px-3 py-1.5 text-xs font-semibold text-amber-300 transition hover:bg-amber-500/20">
                                                            {{ $item['action_label'] ?? 'Open' }}
                                                        </a>
                                                    @endif
                                                    @if ($item['is_completed'])
                                                        <span class="rounded-full bg-emerald-500/10 px-3 py-1 text-xs font-bold text-emerald-400">Completed</span>
                                                        @if ($item['completed_at'])
                                                            <div class="mt-1 text-xs text-gray-500">{{ $item['completed_at']->format('M j, Y') }}</div>
                                                        @endif
                                                    @else
                                                        <span class="rounded-full bg-gray-800 px-3 py-1 text-xs font-bold text-gray-400">Not started</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-app-layout>
