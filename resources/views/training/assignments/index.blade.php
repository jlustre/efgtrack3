<x-app-layout>
    <div class="space-y-6">
        <div class="overflow-visible rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#0B1F3A] via-[#132F55] to-[#0B1F3A] p-6 text-white shadow-lg">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <a href="{{ route('training.index') }}" class="text-sm font-semibold text-[#C8A24A] transition hover:text-[#D8B75F]">&larr; Training Center</a>
                    <h1 class="mt-2 text-3xl font-semibold">My Assignments</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-200">Courses assigned to you with due dates and progress tracking.</p>
                </div>
                @can('manage training')
                    <a href="{{ route('training.assignments.manage') }}" class="inline-flex rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:bg-[#D8B75F]">
                        Assign courses
                    </a>
                @endcan
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="space-y-3">
                @forelse ($rows as $row)
                    @php
                        $assignment = $row['assignment'];
                        $module = $row['module'];
                    @endphp
                    <div class="rounded-lg border border-slate-100 bg-slate-50/80 px-4 py-4">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h2 class="font-semibold text-[#0B1F3A]">{{ $module?->title ?? 'Course' }}</h2>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ str($assignment->status)->replace('_', ' ')->title() }}
                                    @if ($assignment->due_at)
                                        · Due {{ $assignment->due_at->format('M j, Y') }}
                                    @endif
                                    @if ($assignment->assignedBy)
                                        · Assigned by {{ $assignment->assignedBy->name }}
                                    @endif
                                </p>
                                @if ($assignment->notes)
                                    <p class="mt-2 text-sm text-slate-600">{{ $assignment->notes }}</p>
                                @endif
                                <div class="mt-3 h-2 max-w-md rounded-full bg-slate-200">
                                    <div class="h-2 rounded-full bg-[#C8A24A]" style="width: {{ $row['progress_percent'] }}%"></div>
                                </div>
                            </div>
                            <div class="flex shrink-0 flex-col items-start gap-2 lg:items-end">
                                @if ($row['is_overdue'])
                                    <span class="rounded-full bg-red-100 px-2.5 py-1 text-[0.65rem] font-bold uppercase text-red-800">Overdue</span>
                                @endif
                                <span class="rounded-full bg-[#0B1F3A] px-2.5 py-1 text-[0.65rem] font-bold text-[#C8A24A]">{{ $row['progress_percent'] }}%</span>
                                @if ($module)
                                    <a href="{{ route('training.courses.show', $module) }}" class="inline-flex rounded-md bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#132F55]">
                                        Open course
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">You have no course assignments yet. Explore the course catalog to start learning.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
