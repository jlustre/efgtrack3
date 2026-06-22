<div class="space-y-6">
    @if (session('plan_status') === 'dismissed')
        <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">Recommendation dismissed.</div>
    @elseif (session('plan_status') === 'enrolled')
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">Learning path enrollment updated.</div>
    @endif

    <div class="overflow-visible rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#0B1F3A] via-[#132F55] to-[#0B1F3A] p-6 text-white shadow-lg">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <a href="{{ route('training.index') }}" class="text-sm font-semibold text-[#C8A24A] transition hover:text-[#D8B75F]">&larr; Training Center</a>
                <h1 class="mt-2 text-3xl font-semibold">My Learning Plan</h1>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-200">
                    Personalized next steps based on your role, learning paths, assignments, and program progress.
                </p>
            </div>
            @if ($plan['audience_path'])
                <div class="rounded-lg border border-[#C8A24A]/40 bg-white/10 px-4 py-3 text-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Suggested path</p>
                    <p class="mt-1 font-semibold">{{ $plan['audience_path']->name }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Courses completed', 'value' => $plan['stats']['courses_completed'].' / '.$plan['stats']['courses_available'], 'theme' => 'emerald'],
            ['label' => 'Active assignments', 'value' => $plan['stats']['active_assignments'], 'theme' => 'cyan'],
            ['label' => 'Enrolled paths', 'value' => count($plan['enrolled_paths']), 'theme' => 'gold'],
            ['label' => 'Priority actions', 'value' => count($plan['priority_rows']), 'theme' => 'navy'],
        ] as $card)
            <x-tracker-stat-card
                :label="$card['label']"
                :value="$card['value']"
                :theme="$card['theme']"
            />
        @endforeach
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Priority recommendations</h2>
            <div class="mt-4 space-y-3">
                @forelse ($plan['priority_rows'] as $row)
                    @php $recommendation = $row['recommendation']; @endphp
                    <div class="rounded-lg border border-slate-100 bg-slate-50/80 px-4 py-4">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div>
                                <span class="rounded-full bg-[#FFF9EA] px-2.5 py-0.5 text-[0.65rem] font-bold uppercase text-[#8A6A1F]">{{ $row['label'] }}</span>
                                <p class="mt-2 text-sm font-medium text-[#0B1F3A]">{{ $recommendation->message }}</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                @if ($row['action_url'])
                                    <a href="{{ $row['action_url'] }}" class="inline-flex rounded-md bg-[#0B1F3A] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#132F55]">{{ $row['action_label'] }}</a>
                                @endif
                                <button type="button" wire:click="dismiss({{ $recommendation->id }})" class="inline-flex rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-white">Dismiss</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">You are caught up. Explore the course catalog to keep growing.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            @if ($plan['audience_path'] && collect($plan['enrolled_paths'])->where('path.id', $plan['audience_path']->id)->isEmpty())
                <div class="rounded-xl border border-[#C8A24A]/30 bg-[#FFF9EA] p-5 shadow-sm">
                    <h3 class="font-semibold text-[#0B1F3A]">Start your role-based path</h3>
                    <p class="mt-2 text-sm text-slate-700">{{ $plan['audience_path']->description }}</p>
                    <button type="button" wire:click="enrollPath('{{ $plan['audience_path']->code }}')" class="mt-4 inline-flex rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B75F]">
                        Enroll in {{ $plan['audience_path']->name }}
                    </button>
                </div>
            @endif

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-[#0B1F3A]">Enrolled paths</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($plan['enrolled_paths'] as $pathRow)
                        <div class="rounded-lg border border-slate-100 bg-slate-50/80 px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <a href="{{ route('training.paths.show', $pathRow['path']) }}" class="font-semibold text-[#0B1F3A] hover:text-[#C8A24A]">{{ $pathRow['path']->name }}</a>
                                    @if ($pathRow['next_module'])
                                        <p class="mt-1 text-xs text-slate-500">Next: {{ $pathRow['next_module']->title }} ({{ $pathRow['next_module_progress'] }}%)</p>
                                    @endif
                                </div>
                                <span class="rounded-full bg-[#0B1F3A] px-2 py-0.5 text-[0.65rem] font-bold text-[#C8A24A]">{{ $pathRow['progress_percent'] }}%</span>
                            </div>
                            <div class="mt-2 h-1.5 rounded-full bg-slate-200">
                                <div class="h-1.5 rounded-full bg-[#C8A24A]" style="width: {{ $pathRow['progress_percent'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-600">Enroll in a learning path to get a structured development plan.</p>
                        <a href="{{ route('training.paths.index') }}" class="mt-2 inline-flex text-sm font-semibold text-[#0B1F3A] underline">Browse learning paths</a>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if (count($plan['all_rows']) > count($plan['priority_rows']))
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">More suggestions</h2>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                @foreach (array_slice($plan['all_rows'], count($plan['priority_rows'])) as $row)
                    @php $recommendation = $row['recommendation']; @endphp
                    <div class="rounded-lg border border-slate-100 bg-slate-50/80 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $row['label'] }}</p>
                        <p class="mt-1 text-sm text-[#0B1F3A]">{{ $recommendation->message }}</p>
                        @if ($row['action_url'])
                            <a href="{{ $row['action_url'] }}" class="mt-2 inline-flex text-xs font-semibold text-[#C8A24A] underline">{{ $row['action_label'] }}</a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
