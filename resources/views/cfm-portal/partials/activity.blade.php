<div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-6">
    <h3 class="text-lg font-semibold text-amber-400 mb-4">Recent Activity</h3>

    @if (count($profile['activityTimeline'] ?? []) > 0)
        <ul class="space-y-2 text-sm mb-5">
            @foreach ($profile['activityTimeline'] as $item)
                <li class="flex justify-between gap-4 border-b border-gray-800/60 pb-2">
                    <span class="text-gray-300">{{ $item['label'] }}</span>
                    <span class="text-gray-500 text-xs whitespace-nowrap">{{ $item['time'] }}</span>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-sm text-gray-500 mb-5">No recent mentor activity recorded.</p>
    @endif

    <h4 class="text-sm font-semibold text-amber-400/90 mb-2">Assignment History</h4>
    @if (count($profile['assignmentHistory'] ?? []) > 0)
        <ul class="space-y-1 text-xs text-gray-400">
            @foreach ($profile['assignmentHistory'] as $history)
                <li>{{ $history['apprentice'] }} — {{ $history['status'] }} ({{ $history['date'] }})</li>
            @endforeach
        </ul>
    @else
        <p class="text-xs text-gray-500">No assignment history yet.</p>
    @endif
</div>
