<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <a href="{{ route('communications.index') }}" class="text-sm font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">← Communication Hub</a>
            <p class="mt-4 text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Archive</p>
            <h1 class="text-2xl font-semibold text-[#0B1F3A]">Past announcements</h1>
            <p class="mt-2 text-sm text-slate-600">Browse expired and archived announcements with filters.</p>
        </div>
        <button
            type="button"
            wire:click="clearFilters"
            class="rounded-lg border border-[#0B1F3A]/15 px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#FFF9EA]"
        >
            Clear filters
        </button>
    </div>

    <div class="grid gap-3 rounded-2xl border border-[#0B1F3A]/10 bg-white p-4 shadow-sm md:grid-cols-2 lg:grid-cols-4">
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Search</label>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Keywords..." class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" />
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Category</label>
            <select wire:model.live="category_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Priority</label>
            <select wire:model.live="priority" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">All priorities</option>
                @foreach ($priorities as $key => $meta)
                    <option value="{{ $key }}">{{ $meta['label'] ?? ucfirst($key) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Author</label>
            <select wire:model.live="author_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">All authors</option>
                @foreach ($authors as $author)
                    <option value="{{ $author->id }}">{{ $author->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Campaign</label>
            <select wire:model.live="campaign_id" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">All campaigns</option>
                @foreach ($campaigns as $campaign)
                    <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Year</label>
            <select wire:model.live="year" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Any year</option>
                @foreach ($years as $yearOption)
                    <option value="{{ $yearOption }}">{{ $yearOption }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500">Month</label>
            <select wire:model.live="month" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Any month</option>
                @foreach (range(1, 12) as $monthOption)
                    <option value="{{ $monthOption }}">{{ \Carbon\Carbon::create(null, $monthOption)->format('F') }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="space-y-3">
        @forelse ($announcements as $announcement)
            @php($priorityMeta = $priorities[$announcement->priority] ?? ['label' => ucfirst($announcement->priority)])
            <article class="rounded-xl border border-[#0B1F3A]/10 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">{{ $announcement->category?->name ?? 'Announcement' }}</p>
                        <h2 class="mt-1 text-lg font-semibold text-[#0B1F3A]">
                            <a href="{{ route('communications.show', $announcement) }}" class="hover:text-[#8A6A1F]">{{ $announcement->title }}</a>
                        </h2>
                        @if ($announcement->summary)
                            <p class="mt-2 text-sm text-slate-600">{{ $announcement->summary }}</p>
                        @endif
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $priorityMeta['label'] }}</span>
                </div>
                <div class="mt-3 flex flex-wrap gap-3 text-xs text-slate-500">
                    <span>Published {{ $announcement->published_at?->format('M j, Y') }}</span>
                    @if ($announcement->expires_at)
                        <span>· Expired {{ $announcement->expires_at->format('M j, Y') }}</span>
                    @endif
                    @if ($announcement->creator)
                        <span>· By {{ $announcement->creator->name }}</span>
                    @endif
                    @if ($announcement->campaign)
                        <span>· {{ $announcement->campaign->name }}</span>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center">
                <p class="text-sm font-semibold text-[#0B1F3A]">No archived announcements match your filters</p>
                <p class="mt-2 text-sm text-slate-500">Try adjusting filters or check back later.</p>
            </div>
        @endforelse
    </div>

    {{ $announcements->links() }}
</div>
