@php
    $onboarding = $overview['onboarding'] ?? [];
    $licensing = $overview['licensing'] ?? [];
    $fap = $overview['fap'] ?? [];
    $training = $overview['training'] ?? [];
    $communications = $overview['communications'] ?? [];
    $performance = $overview['performance'] ?? [];
    $career = $overview['career'] ?? [];
    $upcomingEvents = $overview['upcoming_events'] ?? [];
@endphp

<section class="mt-6">
    <div class="mb-4">
        <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Member Development</p>
        <h2 class="text-lg font-semibold text-[#0B1F3A]">Your Development Journey</h2>
        <p class="mt-1 text-sm text-slate-500">Track onboarding, compliance, training, team communications, performance, and rank advancement in one place.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <x-journey-hub-section
            title="Onboarding & Orientation"
            eyebrow="Getting started"
            :percent="$onboarding['percent'] ?? 0"
            :summary="($onboarding['completed'] ?? 0).' of '.($onboarding['total'] ?? 0).' steps complete'"
            :route="$onboarding['route'] ?? null"
            route-label="Open tracker"
        >
            <ul class="space-y-2">
                @forelse ($onboarding['next_steps'] ?? [] as $step)
                    <li class="flex items-center justify-between gap-3 rounded-md border border-slate-100 bg-slate-50 px-3 py-2">
                        <span class="text-sm font-medium text-[#0B1F3A]">{{ $step['title'] }}</span>
                        <span class="shrink-0 text-[0.68rem] font-semibold uppercase text-[#C8A24A]">{{ $step['status'] }}</span>
                    </li>
                @empty
                    <li class="rounded-md border border-dashed border-slate-200 bg-slate-50 px-3 py-4 text-center text-xs text-slate-500">
                        All onboarding steps are complete.
                    </li>
                @endforelse
            </ul>
        </x-journey-hub-section>

        <x-journey-hub-section
            title="Licensing & Compliance"
            eyebrow="Credentials"
            :percent="$licensing['percent'] ?? 0"
            :summary="($licensing['completed'] ?? 0).' of '.($licensing['total'] ?? 0).' milestones complete'"
            :route="$licensing['route'] ?? null"
            route-label="Open tracker"
        >
            <ul class="space-y-2">
                @forelse ($licensing['next_steps'] ?? [] as $step)
                    <li class="flex items-center justify-between gap-3 rounded-md border border-slate-100 bg-slate-50 px-3 py-2">
                        <span class="text-sm font-medium text-[#0B1F3A]">{{ $step['title'] }}</span>
                        <span class="shrink-0 text-[0.68rem] font-semibold uppercase text-[#C8A24A]">{{ $step['status'] }}</span>
                    </li>
                @empty
                    <li class="rounded-md border border-dashed border-slate-200 bg-slate-50 px-3 py-4 text-center text-xs text-slate-500">
                        All licensing milestones are complete.
                    </li>
                @endforelse
            </ul>
        </x-journey-hub-section>

        <x-journey-hub-section
            title="Field Apprenticeship Program"
            eyebrow="FAP milestones"
            :percent="$fap['percent'] ?? 0"
            :summary="($fap['completed'] ?? 0).' of '.($fap['total'] ?? 0).' milestones complete'"
            :route="$fap['route'] ?? null"
            route-label="Open tracker"
        >
            <ul class="space-y-2">
                @forelse ($fap['next_steps'] ?? [] as $step)
                    <li class="flex items-center justify-between gap-3 rounded-md border border-slate-100 bg-slate-50 px-3 py-2">
                        <span class="text-sm font-medium text-[#0B1F3A]">{{ $step['title'] }}</span>
                        <span class="shrink-0 text-[0.68rem] font-semibold uppercase text-[#C8A24A]">{{ $step['status'] }}</span>
                    </li>
                @empty
                    <li class="rounded-md border border-dashed border-slate-200 bg-slate-50 px-3 py-4 text-center text-xs text-slate-500">
                        All FAP milestones are complete.
                    </li>
                @endforelse
            </ul>
        </x-journey-hub-section>

        <x-journey-hub-section
            title="Training & Assessments"
            eyebrow="Learning"
            :percent="$training['percent'] ?? 0"
            :summary="($training['completed'] ?? 0).' of '.($training['total'] ?? 0).' lessons complete'"
            :route="$training['route'] ?? null"
            route-label="Open training"
        >
            <ul class="space-y-2">
                @if (! empty($training['cfm_training']['show']))
                    <li class="rounded-md border border-[#C8A24A]/30 bg-[#FFF9EA] px-3 py-2 text-sm">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-medium text-[#0B1F3A]">CFM training modules</span>
                            <span class="text-xs font-semibold text-[#0B1F3A]">{{ $training['cfm_training']['percent'] }}%</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-600">
                            {{ $training['cfm_training']['completed'] }} of {{ $training['cfm_training']['total'] }} modules complete.
                            @if (! empty($training['cfm_training']['route']))
                                <a href="{{ route($training['cfm_training']['route']) }}" class="font-semibold text-[#C8A24A] hover:underline">Open CFM training</a>
                            @endif
                        </p>
                    </li>
                @endif

                @if (! is_null($training['assessments_count'] ?? null))
                    <li class="rounded-md border border-slate-100 bg-slate-50 px-3 py-2 text-sm text-[#0B1F3A]">
                        <span class="font-medium">{{ $training['assessments_count'] }}</span>
                        <span class="text-slate-600"> assessment{{ ($training['assessments_count'] ?? 0) === 1 ? '' : 's' }} available</span>
                    </li>
                @endif

                @forelse ($training['next_steps'] ?? [] as $step)
                    <li class="flex items-center justify-between gap-3 rounded-md border border-slate-100 bg-slate-50 px-3 py-2">
                        <span class="text-sm font-medium text-[#0B1F3A]">{{ $step['title'] }}</span>
                        <span class="shrink-0 text-[0.68rem] font-semibold uppercase text-[#C8A24A]">{{ $step['status'] }}</span>
                    </li>
                @empty
                    @if (empty($training['cfm_training']['show']) && is_null($training['assessments_count'] ?? null))
                        <li class="rounded-md border border-dashed border-slate-200 bg-slate-50 px-3 py-4 text-center text-xs text-slate-500">
                            All published lessons are complete.
                        </li>
                    @endif
                @endforelse
            </ul>
        </x-journey-hub-section>

        <x-journey-hub-section
            title="Mentor & Team Communications"
            eyebrow="Stay connected"
            :route="$communications['announcements_route'] ?? null"
            route-label="All announcements"
            col-span="md:col-span-2 xl:col-span-1"
        >
            <div class="mb-4 grid gap-3 sm:grid-cols-2">
                <div class="rounded-md bg-[#0B1F3A] p-4 text-white">
                    <div class="text-xs uppercase tracking-wide text-slate-300">Certified Field Mentor</div>
                    <div class="mt-1 text-lg font-semibold">{{ $communications['mentor']['name'] ?? 'Unassigned' }}</div>
                    @if (! empty($communications['mentor']['email']))
                        <div class="mt-1 truncate text-xs text-slate-300">{{ $communications['mentor']['email'] }}</div>
                    @endif
                </div>

                <div class="rounded-md border border-slate-100 bg-slate-50 p-4">
                    <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Support network</div>
                    <dl class="mt-2 space-y-2 text-sm">
                        <div>
                            <dt class="text-xs text-slate-500">Sponsor</dt>
                            <dd class="font-medium text-[#0B1F3A]">{{ $communications['sponsor']['name'] ?? 'Not assigned' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-500">Team</dt>
                            <dd class="font-medium text-[#0B1F3A]">{{ $communications['team_name'] ?? 'Not assigned' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-500">Open tasks</dt>
                            <dd>
                                <a href="{{ route($communications['tasks_route'] ?? 'tasks.index') }}" class="font-semibold text-[#C8A24A] hover:underline">
                                    {{ $communications['open_tasks'] ?? 0 }} open task{{ ($communications['open_tasks'] ?? 0) === 1 ? '' : 's' }}
                                </a>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mb-4">
                <div class="mb-2 flex items-center justify-between gap-2">
                    <h4 class="text-sm font-semibold text-[#0B1F3A]">Latest announcements</h4>
                    @if (! empty($communications['team_route']))
                        <a href="{{ route($communications['team_route']) }}" class="text-xs font-semibold text-[#C8A24A] hover:underline">View team</a>
                    @endif
                </div>
                <ul class="space-y-2">
                    @forelse ($communications['announcements'] ?? [] as $announcement)
                        <li class="rounded-md border border-slate-100 bg-slate-50 px-3 py-2">
                            <div class="text-sm font-semibold text-[#0B1F3A]">{{ $announcement['title'] }}</div>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $announcement['meta'] }}</p>
                        </li>
                    @empty
                        <li class="rounded-md border border-dashed border-slate-200 bg-slate-50 px-3 py-4 text-center text-xs text-slate-500">
                            No published announcements yet.
                        </li>
                    @endforelse
                </ul>
            </div>

            @if (! empty($upcomingEvents['items']) || ! empty($upcomingEvents['note']))
                <div>
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <h4 class="text-sm font-semibold text-[#0B1F3A]">Upcoming events</h4>
                        @if (! empty($upcomingEvents['route']))
                            <a href="{{ route($upcomingEvents['route']) }}" class="text-xs font-semibold text-[#C8A24A] hover:underline">View calendar</a>
                        @endif
                    </div>

                    @if (! empty($upcomingEvents['note']))
                        <p class="rounded-md border border-dashed border-slate-200 bg-slate-50 px-3 py-3 text-xs text-slate-500">{{ $upcomingEvents['note'] }}</p>
                    @else
                        <ul class="space-y-2">
                            @foreach ($upcomingEvents['items'] as $event)
                                <li class="flex gap-3 rounded-md border border-slate-100 bg-slate-50 px-3 py-2">
                                    <div class="flex h-10 w-12 shrink-0 items-center justify-center rounded-md bg-[#0B1F3A] text-center text-[0.65rem] font-bold uppercase leading-tight text-[#C8A24A]">
                                        {{ $event['date_label'] }}
                                    </div>
                                    <div class="min-w-0">
                                        @if (! empty($event['url']))
                                            <a href="{{ $event['url'] }}" class="text-sm font-semibold text-[#0B1F3A] hover:text-[#C8A24A]">{{ $event['title'] }}</a>
                                        @else
                                            <div class="text-sm font-semibold text-[#0B1F3A]">{{ $event['title'] }}</div>
                                        @endif
                                        <p class="text-xs text-slate-500">{{ $event['time_label'] }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
        </x-journey-hub-section>

        <x-journey-hub-section
            title="Progress & Performance"
            eyebrow="Activity snapshot"
            :percent="$performance['profile_percent'] ?? 0"
            summary="Profile completion and production metrics"
            :route="$performance['profile_route'] ?? null"
            route-label="Edit profile"
        >
            <dl class="grid grid-cols-2 gap-3">
                <div class="rounded-md border border-slate-100 bg-slate-50 px-3 py-3">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Prospects</dt>
                    <dd class="mt-1 text-xl font-bold text-[#0B1F3A]">{{ $performance['prospects'] ?? 0 }}</dd>
                </div>
                <div class="rounded-md border border-slate-100 bg-slate-50 px-3 py-3">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Recruits</dt>
                    <dd class="mt-1 text-xl font-bold text-[#0B1F3A]">{{ $performance['recruits'] ?? 0 }}</dd>
                </div>
                <div class="col-span-2 rounded-md border border-[#C8A24A]/30 bg-[#FFF9EA] px-3 py-3">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-600">Annual production</dt>
                    <dd class="mt-1 text-xl font-bold text-[#0B1F3A]">{{ $performance['production'] ?? '$0' }}</dd>
                </div>
            </dl>
        </x-journey-hub-section>

        <x-journey-hub-section
            title="Career Development & Rank Advancement"
            eyebrow="Advancement path"
            :percent="$career['percent'] ?? 0"
            :summary="($career['next_rank'] ?? null)
                ? 'Working toward '.($career['next_rank']['code'] ?? '').' — '.($career['next_rank']['name'] ?? '')
                : 'You are at the highest active rank tier.'"
            :route="$career['route'] ?? null"
            route-label="Rank advancement"
            col-span="md:col-span-2"
        >
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-[#0B1F3A] px-3 py-1 text-xs font-semibold text-white">
                    Current: {{ $career['current_rank']['code'] ?? 'New Recruit' }}
                </span>
                @if (! empty($career['next_rank']))
                    <span class="rounded-full border border-[#C8A24A]/50 bg-[#FFF9EA] px-3 py-1 text-xs font-semibold text-[#0B1F3A]">
                        Next: {{ $career['next_rank']['code'] }} — {{ $career['next_rank']['name'] }}
                    </span>
                @endif
            </div>

            <ul class="space-y-2">
                @forelse ($career['requirements'] ?? [] as $requirement)
                    <li class="flex items-center justify-between gap-3 rounded-md border border-slate-100 bg-slate-50 px-3 py-2">
                        <span class="text-sm font-medium text-[#0B1F3A]">{{ $requirement['title'] }}</span>
                        <span class="shrink-0 text-[0.68rem] font-semibold uppercase text-[#C8A24A]">{{ $requirement['status'] }}</span>
                    </li>
                @empty
                    <li class="rounded-md border border-dashed border-slate-200 bg-slate-50 px-3 py-4 text-center text-xs text-slate-500">
                        @if (! empty($career['next_rank']))
                            No rank requirements are configured for the next tier yet.
                        @else
                            No further rank requirements at this time.
                        @endif
                    </li>
                @endforelse
            </ul>
        </x-journey-hub-section>
    </div>
</section>
