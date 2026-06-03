<div class="bg-gray-900/30 backdrop-blur-sm border border-gray-800 rounded-2xl p-5 md:p-6 mb-8">
    <div class="flex flex-wrap items-center gap-3 mb-5">
        <div class="flex items-center gap-2">
            <svg class="h-5 w-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
            </svg>
            <h2 class="text-lg font-bold text-white">CFM Rank Structure &amp; Advancement Criteria</h2>
        </div>
        <span class="inline-flex items-center rounded-full bg-amber-900/40 border border-amber-500/30 px-2.5 py-0.5 text-xs font-medium text-amber-300">From Low to High</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($rankStructure['tiers'] ?? [] as $tier)
            <div class="rounded-xl border border-gray-800 bg-gray-900/50 p-4 hover:border-amber-500/20 transition-colors">
                <div class="flex items-center gap-1.5 mb-2">
                    @if($tier->icon === 'trophy')
                        <span class="text-amber-400" aria-hidden="true">🏆</span>
                    @endif
                    <h3 class="text-sm font-bold text-amber-400">{{ $tier->title }}</h3>
                </div>
                <p class="text-xs leading-relaxed text-gray-400">{{ $tier->criteria }}</p>
                @if($tier->next_step)
                    <p class="mt-2 text-xs text-green-400">
                        <span class="text-green-500" aria-hidden="true">→</span>
                        Next: {{ $tier->next_step }}
                    </p>
                @endif
            </div>
        @endforeach
    </div>

    @if(! empty($rankStructure['guideline']))
        <div class="mt-5 pt-4 border-t border-gray-800">
            <p class="text-xs leading-relaxed text-gray-500">
                <span class="mr-1" aria-hidden="true">📌</span>
                {{ $rankStructure['guideline'] }}
            </p>
        </div>
    @endif
</div>
