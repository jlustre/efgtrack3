<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($badges as $badge)
            <div class="rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#FFF9EA] to-white p-5 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#0B1F3A] text-xl text-[#C8A24A]">★</div>
                <h3 class="mt-3 font-semibold text-[#0B1F3A]">{{ $badge->name }}</h3>
                <p class="mt-1 text-xs text-slate-600">{{ $badge->description }}</p>
            </div>
        @endforeach
    </div>

    <div class="rounded-xl border border-slate-200 bg-white/90 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-[#0B1F3A]">Recent Awards</h3>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($recentAwards as $award)
                <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                    <p class="font-semibold text-[#0B1F3A]">{{ $award->cfm->name ?? 'CFM' }}</p>
                    <p class="text-sm text-[#8A6A1F]">{{ $award->badge?->name }}</p>
                    <p class="text-xs text-slate-500">{{ $award->created_at->format('M j, Y') }}</p>
                </div>
            @empty
                <p class="text-sm text-slate-600">Recognition awards will appear here as CFMs earn milestones.</p>
            @endforelse
        </div>
    </div>
</div>
