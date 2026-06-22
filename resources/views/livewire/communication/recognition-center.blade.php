<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <a href="{{ route('communications.index') }}" class="text-sm font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">← Communication Hub</a>
            <p class="mt-4 text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Recognition Center</p>
            <h1 class="text-2xl font-semibold text-[#0B1F3A]">Celebrate wins & milestones</h1>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                New licenses, promotions, FAP graduates, top producers, and team achievements.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($canCreate)
                <a href="{{ route('communications.recognition.create') }}" class="inline-flex items-center rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-[#C8A24A] transition hover:bg-[#132a4d]">
                    Post recognition
                </a>
            @endif
            <a href="{{ route('communications.leadership') }}" class="inline-flex items-center rounded-lg border border-[#0B1F3A]/15 px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:border-[#C8A24A]/50 hover:bg-[#FFF9EA]">
                Leadership Desk
            </a>
        </div>
    </div>

    @if ($recentAwards->isNotEmpty())
        <section class="rounded-2xl border border-[#C8A24A]/30 bg-[#FFF9EA] p-5">
            <p class="text-xs font-semibold uppercase tracking-wide text-[#8A6A1F]">Recent badge awards</p>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($recentAwards as $award)
                    <div class="inline-flex items-center gap-2 rounded-full border border-[#C8A24A]/40 bg-white px-3 py-1.5 text-sm">
                        <span>{{ $award->badge?->icon }}</span>
                        <span class="font-semibold text-[#0B1F3A]">{{ $award->user?->name }}</span>
                        <span class="text-slate-500">· {{ $award->badge?->name }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="rounded-2xl border border-[#0B1F3A]/10 bg-white p-4 shadow-sm">
        <input
            type="search"
            wire:model.live.debounce.300ms="search"
            placeholder="Search recognition posts..."
            class="w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
        >
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($postsWithContext as $item)
            @php
                $announcement = $item['announcement'];
                $context = $item['context'];
                $engagement = $item['engagement'];
            @endphp
            <article class="flex flex-col overflow-hidden rounded-2xl border border-[#C8A24A]/30 bg-white shadow-sm transition hover:border-[#C8A24A]">
                <div class="bg-gradient-to-br from-[#0B1F3A] to-[#132a4d] px-5 py-4 text-white">
                    @if ($context['badge'])
                        <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-sm">
                            <span>{{ $context['badge']['icon'] }}</span>
                            <span class="font-semibold text-[#C8A24A]">{{ $context['badge']['name'] }}</span>
                        </div>
                    @endif
                    @if ($context['honoree'])
                        <p class="mt-3 text-xs uppercase tracking-wide text-slate-300">Honoree</p>
                        <p class="text-lg font-semibold">{{ $context['honoree']['name'] }}</p>
                    @endif
                </div>
                <div class="flex flex-1 flex-col p-5">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">
                        <a href="{{ route('communications.show', $announcement) }}" class="hover:text-[#8A6A1F]">
                            {{ $announcement->title }}
                        </a>
                    </h2>
                    @if ($announcement->summary)
                        <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $announcement->summary }}</p>
                    @endif
                    <div class="mt-auto flex items-center justify-between pt-4 text-xs text-slate-500">
                        <span>{{ $announcement->published_at?->diffForHumans() }}</span>
                        <span>
                            @if ($engagement['reactions'] > 0){{ $engagement['reactions'] }} reactions @endif
                            @if ($engagement['comments'] > 0) · {{ $engagement['comments'] }} comments @endif
                        </span>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center">
                <p class="text-sm font-semibold text-[#0B1F3A]">No recognition posts yet</p>
                <p class="mt-2 text-sm text-slate-500">Celebrate team wins here as they happen.</p>
            </div>
        @endforelse
    </div>

    {{ $posts->links() }}
</div>
