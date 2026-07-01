<div>
    <div
        @class([
            'fixed inset-0 z-50 flex items-center justify-center p-4',
            'hidden' => ! $show,
        ])
        role="dialog"
        aria-modal="true"
        aria-labelledby="my-tasks-modal-title"
        @if (! $show) aria-hidden="true" @endif
    >
        <div class="absolute inset-0 bg-[#0B1F3A]/60" wire:click="close"></div>
        <div class="relative z-10 flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-lg border border-[#C8A24A]/40 bg-white shadow-xl">
            <div class="border-b border-slate-200 bg-[#0B1F3A] px-6 py-4 text-white">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-[#C8A24A]">My Tasks</p>
                        <h3 id="my-tasks-modal-title" class="mt-1 text-lg font-semibold">Open tasks by priority</h3>
                        <p class="mt-1 text-sm text-slate-300">
                            {{ $tasks['count'] }} open task{{ $tasks['count'] === 1 ? '' : 's' }} sorted from highest to lowest priority.
                        </p>
                    </div>
                    <button type="button" wire:click="close" class="text-2xl leading-none text-slate-300 hover:text-white">&times;</button>
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto p-4">
                @forelse ($tasks['items'] as $task)
                    <div
                        wire:key="my-task-{{ md5(($task['title'] ?? '').($task['meta'] ?? '')) }}"
                        @class([
                            'mb-2 rounded-lg border px-4 py-3 transition',
                            ($task['highlight'] ?? false)
                                ? 'border-[#C8A24A]/40 bg-[#FFFDF5]'
                                : 'border-slate-200 bg-slate-50',
                        ])
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                @if (filled($task['category'] ?? null))
                                    <span @class([
                                        'mb-2 inline-flex rounded-full border px-2 py-0.5 text-[0.65rem] font-bold uppercase tracking-wide',
                                        $task['category_accent'] ?? 'bg-slate-100 text-slate-700 border-slate-200',
                                    ])>
                                        {{ $task['category'] }}
                                    </span>
                                @endif

                                @include('dashboard.partials.activity-item-content', ['item' => $task])
                            </div>

                            @if (filled($task['url'] ?? null))
                                <a
                                    href="{{ $task['url'] }}"
                                    class="inline-flex shrink-0 items-center gap-1 rounded-md bg-[#0B1F3A] px-3 py-2 text-xs font-semibold text-white transition hover:bg-[#132d52]"
                                    wire:click="close"
                                >
                                    {{ $task['action_label'] ?? 'Open' }}
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-10 text-center">
                        <p class="text-sm font-semibold text-[#0B1F3A]">You are all caught up</p>
                        <p class="mt-1 text-xs text-slate-500">No open tasks need your attention right now.</p>
                    </div>
                @endforelse
            </div>

            <div class="flex items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                <p class="text-xs text-slate-500">Use the action link to open the workflow for that task category.</p>
                @if (Route::has('tasks.index'))
                    <a
                        href="{{ route('tasks.index') }}"
                        class="inline-flex items-center gap-1 rounded-lg bg-[#0B1F3A] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#132d52]"
                        wire:click="close"
                    >
                        Open task manager
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
