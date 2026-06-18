<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-8">
        @foreach ($data['cards'] as $card)
            <div class="rounded-xl border border-white/60 bg-white/80 p-4 shadow-sm backdrop-blur-sm">
                <p class="text-[0.68rem] font-semibold uppercase tracking-wide text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-2 text-xl font-bold {{ $card['accent'] }}">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="rounded-xl border border-[#C8A24A]/30 bg-gradient-to-r from-[#FFF9EA] to-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap gap-6">
                <div>
                    <p class="text-[0.68rem] font-semibold uppercase tracking-wide text-[#8A6A1F]">Academy Points</p>
                    <p class="mt-1 text-2xl font-bold text-[#0B1F3A]">{{ number_format($data['gamification']['points']) }}</p>
                </div>
                <div>
                    <p class="text-[0.68rem] font-semibold uppercase tracking-wide text-[#8A6A1F]">Learning Streak</p>
                    <p class="mt-1 text-2xl font-bold text-orange-600">{{ $data['gamification']['streak'] }}<span class="ml-1 text-sm font-semibold text-slate-500">days</span></p>
                </div>
                <div>
                    <p class="text-[0.68rem] font-semibold uppercase tracking-wide text-[#8A6A1F]">Badges</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-700">{{ $data['gamification']['badge_count'] }}</p>
                </div>
                @if ($data['gamification']['rank'])
                    <div>
                        <p class="text-[0.68rem] font-semibold uppercase tracking-wide text-[#8A6A1F]">Rank</p>
                        <p class="mt-1 text-2xl font-bold text-[#0B1F3A]">#{{ $data['gamification']['rank'] }}</p>
                    </div>
                @endif
            </div>
            <a href="{{ route('training.achievements.index') }}" class="inline-flex rounded-md bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#132F55]">
                View achievements
            </a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white/90 p-6 shadow-sm backdrop-blur-sm xl:col-span-2">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Learning Activity</h2>
                    <p class="mt-1 text-xs text-slate-500">Lessons started and completed over the last six months</p>
                </div>
                <span class="rounded-full bg-[#FFF9EA] px-3 py-1 text-xs font-semibold text-[#8A6A1F]">
                    {{ $data['lesson_completion_percent'] }}% lesson completion
                </span>
            </div>
            <div class="mt-6 flex h-40 items-end gap-3">
                @foreach ($data['monthly_activity'] as $month)
                    <div class="flex min-w-0 flex-1 flex-col items-center gap-2">
                        <div class="flex h-32 w-full items-end justify-center gap-1">
                            <div class="w-3 rounded-t bg-sky-300" style="height: {{ max(4, ($month['started'] / $activityMax) * 100) }}%"></div>
                            <div class="w-3 rounded-t bg-[#0B1F3A]" style="height: {{ max(4, ($month['completed'] / $activityMax) * 100) }}%"></div>
                        </div>
                        <span class="text-xs font-semibold text-slate-500">{{ $month['month'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex gap-4 text-xs text-slate-600">
                <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-sky-300"></span> Started</span>
                <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-[#0B1F3A]"></span> Completed</span>
            </div>
        </div>

        <div class="rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#FFF9EA] to-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-[#0B1F3A]">Recommended For You</h2>
                <a href="{{ route('training.plan.index') }}" class="text-xs font-semibold text-[#0B1F3A] underline">My learning plan</a>
            </div>
            <ul class="mt-4 space-y-3">
                @foreach ($data['recommendations'] as $row)
                    <li class="rounded-lg border border-[#C8A24A]/20 bg-white/80 px-3 py-2 text-sm text-[#0B1F3A]">
                        <p>{{ $row['recommendation']->message }}</p>
                        @if ($row['action_url'])
                            <a href="{{ $row['action_url'] }}" class="mt-2 inline-flex text-xs font-semibold text-[#8A6A1F] underline">{{ $row['action_label'] }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-[#0B1F3A]">Learning Paths</h2>
                <a href="{{ route('training.paths.index') }}" class="text-xs font-semibold text-[#0B1F3A] underline">View all</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($data['learning_paths'] as $path)
                    <a href="{{ route('training.paths.show', $path['code']) }}" class="block rounded-lg border border-slate-100 bg-slate-50/80 p-4 transition hover:border-[#C8A24A]/40 hover:bg-[#FFF9EA]">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-[#0B1F3A]">{{ $path['name'] }}</h3>
                                <p class="mt-1 text-sm text-slate-600">{{ $path['description'] }}</p>
                                <p class="mt-2 text-xs text-slate-500">{{ $path['module_count'] }} courses · {{ str($path['status'])->replace('_', ' ')->title() }}</p>
                            </div>
                            <span class="rounded-full bg-[#0B1F3A] px-2.5 py-1 text-xs font-bold text-[#C8A24A]">{{ $path['progress_percent'] }}%</span>
                        </div>
                        <div class="mt-3 h-2 rounded-full bg-slate-200">
                            <div class="h-2 rounded-full bg-[#C8A24A]" style="width: {{ $path['progress_percent'] }}%"></div>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-slate-600">Learning paths will appear here once published by your administrator.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-[#0B1F3A]">Featured Courses</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($data['featured_courses'] as $course)
                        <a href="{{ route('training.courses.show', $course) }}" class="flex items-start justify-between gap-3 rounded-lg border border-slate-100 bg-slate-50/80 px-4 py-3 transition hover:border-[#C8A24A]/40 hover:bg-[#FFF9EA]">
                            <div>
                                <p class="font-semibold text-[#0B1F3A]">{{ $course->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $course->category?->name }} · {{ str($course->course_type ?? 'video')->replace('_', ' ')->title() }}</p>
                            </div>
                            <span class="rounded-full bg-[#FFF9EA] px-2 py-0.5 text-[0.65rem] font-bold uppercase text-[#8A6A1F]">{{ $course->difficulty ?? 'beginner' }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-600">Featured academy courses will appear here once published.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-[#0B1F3A]">Program Trackers</h2>
                <p class="mt-1 text-xs text-slate-500">Checklist-based development programs integrated with the academy</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($data['checklist_links'] as $link)
                        <a href="{{ route($link['route']) }}" class="inline-flex items-center rounded-full border border-[#C8A24A]/40 bg-[#FFF9EA] px-3 py-1.5 text-xs font-semibold text-[#0B1F3A] transition hover:bg-[#C8A24A]/20">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                    <a href="{{ route('training.coaching.index') }}" class="inline-flex items-center rounded-full border border-[#C8A24A]/40 bg-[#FFF9EA] px-3 py-1.5 text-xs font-semibold text-[#0B1F3A] transition hover:bg-[#C8A24A]/20">
                        FAP & Coaching Center
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if ($data['recent_badges']->isNotEmpty())
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-[#0B1F3A]">Recent Achievements</h2>
                <a href="{{ route('training.achievements.index') }}" class="text-xs font-semibold text-[#0B1F3A] underline">View all</a>
            </div>
            <div class="mt-4 flex flex-wrap gap-3">
                @foreach ($data['recent_badges'] as $userBadge)
                    <div class="rounded-full border border-[#C8A24A]/30 bg-[#FFF9EA] px-4 py-2 text-sm font-semibold text-[#0B1F3A]">
                        {{ $userBadge->badge?->name }}
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-[#0B1F3A]">My Assignments</h2>
                <a href="{{ route('training.assignments.index') }}" class="text-xs font-semibold text-[#0B1F3A] underline">View all</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($assignmentRows as $row)
                    <div class="rounded-lg border border-slate-100 bg-slate-50/80 px-4 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-[#0B1F3A]">{{ $row['module']?->title }}</p>
                                <p class="mt-1 text-xs text-slate-500">
                                    @if ($row['assignment']->due_at) Due {{ $row['assignment']->due_at->format('M j, Y') }} @endif
                                    @if ($row['is_overdue']) · <span class="font-semibold text-red-700">Overdue</span> @endif
                                </p>
                            </div>
                            <span class="rounded-full bg-[#0B1F3A] px-2 py-0.5 text-[0.65rem] font-bold text-[#C8A24A]">{{ $row['progress_percent'] }}%</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">No active assignments.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-[#0B1F3A]">My Certifications</h2>
                <a href="{{ route('training.certifications.index') }}" class="text-xs font-semibold text-[#0B1F3A] underline">View all</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($certificationRows as $row)
                    <div class="rounded-lg border border-slate-100 bg-slate-50/80 px-4 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-[#0B1F3A]">{{ $row['certification']?->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $row['status_label'] }}</p>
                            </div>
                            @if ($row['record']->certificate_number)
                                <span class="text-[0.65rem] font-semibold text-slate-500">{{ $row['record']->certificate_number }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">Earn certifications by completing courses and assessments.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-[#0B1F3A]">Course Catalog</h2>
                <p class="mt-1 text-xs text-slate-500">All published academy courses</p>
            </div>
        </div>
        <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @forelse ($courseCatalog as $entry)
                @php
                    $course = $entry['module'];
                    $percent = $entry['progress_percent'];
                @endphp
                <a href="{{ route('training.courses.show', $course) }}" class="rounded-lg border border-slate-100 bg-slate-50/80 p-4 transition hover:border-[#C8A24A]/40 hover:bg-[#FFF9EA]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-[#0B1F3A]">{{ $course->title }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $course->category?->name }} · {{ $course->lessons->count() }} lessons</p>
                        </div>
                        <span class="rounded-full bg-[#0B1F3A] px-2 py-0.5 text-[0.65rem] font-bold text-[#C8A24A]">{{ $percent }}%</span>
                    </div>
                    <div class="mt-3 h-1.5 rounded-full bg-slate-200">
                        <div class="h-1.5 rounded-full bg-[#C8A24A]" style="width: {{ $percent }}%"></div>
                    </div>
                </a>
            @empty
                <p class="text-sm text-slate-600 md:col-span-2 xl:col-span-3">Published courses will appear here once your administrator adds them.</p>
            @endforelse
        </div>
    </div>
</div>
