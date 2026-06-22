<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <a href="{{ route('communications.index') }}" class="text-sm font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">← Communication Hub</a>
            <p class="mt-4 text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Campaign Center</p>
            <h1 class="text-2xl font-semibold text-[#0B1F3A]">Agency challenges</h1>
            <p class="mt-2 text-sm text-slate-600">Recruiting, production, licensing, and training campaigns with live leaderboards.</p>
        </div>
        @if ($canManage)
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('communications.campaigns.create') }}" class="rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-[#C8A24A]">Create campaign</a>
                <a href="{{ route('communications.events.create') }}" class="rounded-lg border border-[#0B1F3A]/15 px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#FFF9EA]">Event announcement</a>
            </div>
        @endif
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        @forelse ($campaigns as $campaign)
            @php($typeMeta = config('communication-hub.campaign_types.'.$campaign->type, []))
            <article class="overflow-hidden rounded-2xl border border-[#C8A24A]/30 bg-white shadow-sm">
                <div class="bg-gradient-to-r from-[#0B1F3A] to-[#132a4d] px-5 py-4 text-white">
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">{{ $typeMeta['label'] ?? ucfirst($campaign->type) }}</p>
                    <h2 class="mt-2 text-xl font-semibold">{{ $campaign->name }}</h2>
                </div>
                <div class="space-y-3 p-5">
                    @if ($campaign->description)
                        <p class="text-sm leading-6 text-slate-600">{{ $campaign->description }}</p>
                    @endif
                    <div class="flex flex-wrap gap-3 text-xs text-slate-500">
                        @if ($campaign->starts_at)
                            <span>Starts {{ $campaign->starts_at->format('M j, Y') }}</span>
                        @endif
                        @if ($campaign->ends_at)
                            <span>· Ends {{ $campaign->ends_at->format('M j, Y') }}</span>
                        @endif
                        <span>· Metric: {{ $typeMeta['unit'] ?? $campaign->leaderboard_metric }}</span>
                    </div>
                    <a href="{{ route('communications.campaigns.show', $campaign) }}" class="inline-flex rounded-lg border border-[#C8A24A]/50 bg-[#FFF9EA] px-3 py-2 text-sm font-semibold text-[#8A6A1F]">
                        View leaderboard
                    </a>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center">
                <p class="text-sm font-semibold text-[#0B1F3A]">No active campaigns</p>
                <p class="mt-2 text-sm text-slate-500">New challenges will appear here when leadership launches them.</p>
            </div>
        @endforelse
    </div>
</div>
