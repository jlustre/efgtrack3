@if (count($portal['rankTiers']) > 0)
    <div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-6 mb-8">
        <h3 class="text-lg font-semibold text-amber-400 mb-4">CFM Rank Structure</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            @foreach ($portal['rankTiers'] as $tier)
                <div class="rounded-xl border border-gray-700/60 bg-gray-800/40 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        @if ($tier->icon)
                            <span class="text-xl" aria-hidden="true">{{ $tier->icon }}</span>
                        @endif
                        <h4 class="font-semibold text-white">{{ $tier->title }}</h4>
                    </div>
                    <p class="text-xs text-gray-400 leading-relaxed">{{ $tier->criteria }}</p>
                    @if ($tier->next_step)
                        <p class="text-xs text-amber-400/80 mt-2">Next: {{ $tier->next_step }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        @if ($portal['advancementGuideline'])
            <p class="text-sm text-gray-400 mt-5 border-t border-gray-800 pt-4">{{ $portal['advancementGuideline'] }}</p>
        @endif
    </div>
@endif
