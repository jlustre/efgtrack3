<x-app-layout>
    <div class="space-y-6">
        <div class="overflow-visible rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#0B1F3A] via-[#132F55] to-[#0B1F3A] p-6 text-white shadow-lg">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">{{ config('training-academy.brand.name') }}</p>
                    <h1 class="mt-2 text-3xl font-semibold">Assessments</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-200">
                        Validate your learning with knowledge checks tied to academy courses.
                    </p>
                </div>
                <a href="{{ route('training.index') }}" class="inline-flex items-center rounded-md border border-[#C8A24A]/50 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#C8A24A]/20">
                    Training Center
                </a>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Available assessments</h2>
            <p class="mt-1 text-xs text-slate-500">Passing score and attempt limits apply per assessment.</p>

            <div class="mt-5 space-y-3">
                @forelse ($rows as $row)
                    @php
                        $assessment = $row['assessment'];
                        $statusLabel = $row['passed']
                            ? 'Passed'
                            : ($row['attempts_count'] > 0 ? 'In progress' : 'Not started');
                    @endphp
                    <div class="rounded-lg border border-slate-100 bg-slate-50/80 px-4 py-4">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h3 class="font-semibold text-[#0B1F3A]">{{ $assessment->title }}</h3>
                                @if ($assessment->module)
                                    <p class="mt-1 text-xs text-slate-500">{{ $assessment->module->title }} · {{ $assessment->module->category?->name }}</p>
                                @endif
                                @if ($assessment->description)
                                    <p class="mt-2 text-sm text-slate-600">{{ $assessment->description }}</p>
                                @endif
                                <div class="mt-2 flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-wide">
                                    <span class="rounded-full bg-white px-2.5 py-1 text-slate-600">Pass {{ $assessment->passing_score }}%</span>
                                    <span class="rounded-full bg-white px-2.5 py-1 text-slate-600">{{ $row['attempts_count'] }} attempts</span>
                                    @if ($row['best_score'] !== null)
                                        <span class="rounded-full bg-white px-2.5 py-1 text-slate-600">Best {{ $row['best_score'] }}%</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex shrink-0 flex-col items-start gap-2 lg:items-end">
                                <span class="rounded-full px-2.5 py-1 text-[0.65rem] font-bold uppercase {{ $row['passed'] ? 'bg-emerald-100 text-emerald-800' : 'bg-[#FFF9EA] text-[#8A6A1F]' }}">
                                    {{ $statusLabel }}
                                </span>
                                @if ($row['can_take'])
                                    <a href="{{ route('assessments.show', $assessment) }}" class="inline-flex rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:bg-[#D8B75F]">
                                        {{ $row['attempts_count'] > 0 ? 'Retake' : 'Start' }}
                                    </a>
                                @else
                                    <a href="{{ route('assessments.show', $assessment) }}" class="inline-flex rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:bg-white">
                                        View details
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">Published assessments will appear here once your administrator adds questions.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
