<div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-6">
    <h3 class="text-lg font-semibold text-amber-400 mb-4">Calendar &amp; Availability</h3>

    @if (! empty($profile['calendarPreview']))
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Booked this week</dt><dd class="text-white">{{ $profile['calendarPreview']['bookedSessions'] ?? 0 }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Open slots this week</dt><dd class="text-white">{{ $profile['calendarPreview']['slotsThisWeek'] ?? 0 }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Calendar busyness</dt><dd class="text-white">{{ $profile['calendarBusyness'] }}%</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Overdue tasks</dt><dd @class(['font-medium', 'text-red-400' => $profile['overdueTasks'] > 0, 'text-white' => $profile['overdueTasks'] === 0])>{{ $profile['overdueTasks'] }}</dd></div>
        </dl>

        @if (! empty($profile['nextSlots']))
            <div class="mt-4 border-t border-gray-800 pt-4">
                <p class="text-xs text-gray-500 mb-2">Upcoming open slots</p>
                <ul class="space-y-1 text-xs text-gray-400">
                    @foreach ($profile['nextSlots'] as $slot)
                        <li>· {{ $slot }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (! empty($profile['calendarPreview']['conflictWarning']))
            <p class="mt-4 text-sm text-red-400 font-medium">Calendar conflict warning — review your schedule.</p>
        @endif
    @else
        <p class="text-sm text-gray-500">No calendar data available.</p>
    @endif
</div>
