<div class="space-y-6">
    @if (session('training_status') === 'lesson-completed')
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            Lesson marked complete.
        </div>
    @elseif (session('training_status') === 'lesson-reopened')
        <div class="rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-800">
            Lesson reopened for review.
        </div>
    @endif

    <div class="overflow-visible rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#0B1F3A] via-[#132F55] to-[#0B1F3A] p-6 text-white shadow-lg">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <a href="{{ route('training.courses.show', $module) }}" class="text-sm font-semibold text-[#C8A24A] transition hover:text-[#D8B75F]">&larr; {{ $module->title }}</a>
                <h1 class="mt-2 text-2xl font-semibold lg:text-3xl">{{ $lesson->title }}</h1>
                <p class="mt-2 text-sm text-slate-300">{{ str($lesson->lesson_type ?? 'video')->replace('_', ' ')->title() }}</p>
            </div>
            <div class="min-w-[10rem] rounded-xl border border-white/20 bg-white/10 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-300">Course progress</p>
                <p class="mt-1 text-2xl font-bold text-[#C8A24A]">{{ $progressPercent }}%</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                @php
                    $embedUrl = null;
                    if ($lesson->video_url && preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $lesson->video_url, $matches)) {
                        $embedUrl = 'https://www.youtube.com/embed/'.$matches[1];
                    }
                @endphp

                @if ($embedUrl)
                    <div class="aspect-video overflow-hidden rounded-lg bg-black">
                        <iframe
                            src="{{ $embedUrl }}"
                            title="{{ $lesson->title }}"
                            class="h-full w-full"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                    </div>
                @elseif ($lesson->external_url)
                    <div class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                        <p class="text-sm text-slate-600">This lesson links to an external resource.</p>
                        <a href="{{ $lesson->external_url }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex rounded-md bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#132F55]">
                            Open resource
                        </a>
                    </div>
                @endif

                @if ($lesson->content)
                    <div class="prose prose-slate mt-6 max-w-none text-slate-700">
                        {!! nl2br(e($lesson->content)) !!}
                    </div>
                @elseif (! $embedUrl && ! $lesson->external_url)
                    <p class="text-sm text-slate-600">Lesson content will be available here once published by your administrator.</p>
                @endif
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap gap-2">
                    @if ($previousLesson)
                        <a href="{{ route('training.lessons.show', [$module, $previousLesson]) }}" class="inline-flex rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:bg-slate-50">
                            Previous
                        </a>
                    @endif
                    @if ($nextLesson && $nextAccessible)
                        <a href="{{ route('training.lessons.show', [$module, $nextLesson]) }}" class="inline-flex rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:bg-slate-50">
                            Next lesson
                        </a>
                    @endif
                </div>

                <div class="flex flex-wrap gap-2">
                    @if ($progress?->isCompleted())
                        <button type="button" wire:click="reopen" class="inline-flex rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:bg-slate-50">
                            Reopen lesson
                        </button>
                    @endif
                    <button
                        type="button"
                        wire:click="markComplete"
                        wire:loading.attr="disabled"
                        class="inline-flex rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:bg-[#D8B75F] disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="markComplete">{{ $progress?->isCompleted() ? 'Completed' : 'Mark complete' }}</span>
                        <span wire:loading wire:target="markComplete">Saving...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Lessons</h2>
            <ol class="mt-4 space-y-2">
                @foreach ($lessonRows as $index => $row)
                    @php
                        $item = $row['lesson'];
                        $isCurrent = (int) $item->id === (int) $lesson->id;
                    @endphp
                    <li class="rounded-lg px-3 py-2 {{ $isCurrent ? 'bg-[#FFF9EA] ring-1 ring-[#C8A24A]/40' : 'bg-slate-50/80' }}">
                        <div class="flex items-start gap-2">
                            <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[0.65rem] font-bold {{ $isCurrent ? 'bg-[#0B1F3A] text-[#C8A24A]' : 'bg-slate-200 text-slate-600' }}">{{ $index + 1 }}</span>
                            <div class="min-w-0">
                                @if ($row['locked'])
                                    <p class="text-sm font-medium text-slate-500">{{ $item->title }}</p>
                                @else
                                    <a href="{{ route('training.lessons.show', [$module, $item]) }}" class="text-sm font-medium {{ $isCurrent ? 'text-[#0B1F3A]' : 'text-[#0B1F3A] hover:text-[#C8A24A]' }}">{{ $item->title }}</a>
                                @endif
                                @if ($row['status'] === 'completed')
                                    <p class="text-[0.65rem] font-semibold uppercase text-emerald-700">Done</p>
                                @elseif ($row['locked'])
                                    <p class="text-[0.65rem] font-semibold uppercase text-amber-700">Locked</p>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</div>
