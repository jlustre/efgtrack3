<div>
    <div
        @class([
            'fixed inset-0 z-50 flex items-center justify-center p-4',
            'hidden' => ! $show,
        ])
        role="dialog"
        aria-modal="true"
        aria-labelledby="prospect-log-activity-picker-title"
        @if (! $show) aria-hidden="true" @endif
    >
        <div class="absolute inset-0 bg-[#0B1F3A]/60" wire:click="close"></div>
        <div class="relative z-10 flex max-h-[90vh] w-full max-w-lg flex-col overflow-hidden rounded-lg border border-[#C8A24A]/40 bg-white shadow-xl">
            <div class="border-b border-slate-200 bg-[#0B1F3A] px-6 py-4 text-white">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-[#C8A24A]">Log Activity</p>
                        <h3 id="prospect-log-activity-picker-title" class="mt-1 text-lg font-semibold">Choose a prospect</h3>
                        <p class="mt-1 text-sm text-slate-300">Select who this call, meeting, or note is for.</p>
                    </div>
                    <button type="button" wire:click="close" class="text-2xl leading-none text-slate-300 hover:text-white">&times;</button>
                </div>
            </div>

            <div class="border-b border-slate-200 p-4">
                <label for="prospect-log-activity-search" class="sr-only">Search prospects</label>
                <input
                    id="prospect-log-activity-search"
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search prospects by name..."
                    class="w-full rounded-lg border-slate-300 text-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
                />
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto p-4">
                @forelse ($prospects as $prospect)
                    <button
                        type="button"
                        wire:key="log-activity-prospect-{{ $prospect->id }}"
                        wire:click="selectProspect('{{ $prospect->id }}')"
                        class="mb-2 flex w-full items-center justify-between gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-left transition hover:border-[#C8A24A]/50 hover:bg-[#FFF9EA]"
                    >
                        <span class="text-sm font-semibold text-[#0B1F3A]">{{ $prospect->displayName() }}</span>
                        <span class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Log</span>
                    </button>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">
                        <p class="text-sm font-semibold text-[#0B1F3A]">No active prospects found</p>
                        <p class="mt-1 text-xs text-slate-500">
                            @if ($search !== '')
                                Try a different search or clear the filter.
                            @else
                                Add a prospect first, then log activity from here.
                            @endif
                        </p>
                        @if (Route::has('team.prospects.create'))
                            <a href="{{ route('team.prospects.create') }}" class="mt-4 inline-flex rounded-lg bg-[#0B1F3A] px-4 py-2 text-xs font-semibold text-white hover:bg-[#132d52]">
                                Add prospect
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
