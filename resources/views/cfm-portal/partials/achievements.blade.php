<div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-6">
    <h3 class="text-lg font-semibold text-amber-400 mb-4">Achievements</h3>

    <ul class="space-y-3">
        @foreach ($achievements as $achievement)
            <li class="flex items-start gap-3 rounded-xl bg-gray-800/50 border border-gray-700/50 p-3">
                <span class="text-2xl leading-none" aria-hidden="true">{{ $achievement['icon'] }}</span>
                <div>
                    <p class="font-medium text-white">{{ $achievement['title'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $achievement['description'] }}</p>
                </div>
            </li>
        @endforeach
    </ul>
</div>
