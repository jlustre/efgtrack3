<div class="space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white/90 p-6 shadow-sm backdrop-blur-sm">
        <h2 class="text-lg font-semibold text-[#0B1F3A]">Pending Mentor Reviews</h2>
        <p class="mt-1 text-sm text-slate-600">Your feedback is anonymous and only shown as aggregated trends — never individual ratings.</p>

        @if ($reviews->isEmpty())
            <p class="mt-6 text-sm text-slate-600">No pending reviews at this time. Reviews are opened at milestones such as 30, 60, and 90 days, FAP completion, and licensing completion.</p>
        @else
            <div class="mt-6 space-y-3">
                @foreach ($reviews as $review)
                    <div class="flex flex-col gap-3 rounded-lg border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-semibold text-[#0B1F3A]">{{ config("cfm-effectiveness.review_triggers.{$review->trigger_type}.label") ?? ucfirst(str_replace('_', ' ', $review->trigger_type)) }}</p>
                            <p class="text-sm text-slate-600">Mentor: {{ $review->cfm->name ?? 'Your CFM' }}</p>
                            @if ($review->due_at)
                                <p class="text-xs text-slate-500">Due {{ $review->due_at->format('M j, Y') }}</p>
                            @endif
                        </div>
                        <a href="{{ route('cfm.effectiveness.reviews.show', $review) }}" class="inline-flex items-center justify-center rounded-lg bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#FFF9EA]">
                            Complete Review
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
