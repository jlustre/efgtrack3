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

    @if ($portal['canEditProfile'] ?? false)
        <div class="mt-6 border-t border-gray-800 pt-5">
            <div class="mb-3">
                <p class="text-sm font-semibold text-white">Share My Calendar</p>
                <p class="mt-1 text-xs text-gray-500">Let trainees and your agency owner see your scheduled events on their calendar. Private events stay hidden.</p>
            </div>

            <form method="POST" action="{{ route('cfm.portal.calendar-sharing.update') }}" class="space-y-3">
                @csrf
                @method('PATCH')

                <label class="flex items-start gap-3 rounded-xl border border-gray-800 bg-gray-950/40 px-3 py-3 text-sm text-gray-200">
                    <input
                        type="checkbox"
                        name="share_calendar_with_apprentices"
                        value="1"
                        class="mt-0.5 rounded border-gray-600 bg-gray-900 text-amber-400 focus:ring-amber-400"
                        @checked(old('share_calendar_with_apprentices', $profile['shareCalendarWithApprentices'] ?? true))
                    >
                    <span>
                        <span class="font-medium text-white">Share with my trainees</span>
                        <span class="mt-1 block text-xs text-gray-500">Active apprentices can view your non-private calendar events.</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 rounded-xl border border-gray-800 bg-gray-950/40 px-3 py-3 text-sm text-gray-200">
                    <input
                        type="checkbox"
                        name="share_calendar_with_agency_owner"
                        value="1"
                        class="mt-0.5 rounded border-gray-600 bg-gray-900 text-amber-400 focus:ring-amber-400"
                        @checked(old('share_calendar_with_agency_owner', $profile['shareCalendarWithAgencyOwner'] ?? false))
                    >
                    <span>
                        <span class="font-medium text-white">Share with agency owner</span>
                        <span class="mt-1 block text-xs text-gray-500">
                            @if (($profile['agencyOwner'] ?? '—') !== '—')
                                {{ $profile['agencyOwner'] }} can view your non-private calendar events.
                            @else
                                Your agency owner can view your non-private calendar events when assigned.
                            @endif
                        </span>
                    </span>
                </label>

                <button type="submit" class="inline-flex items-center rounded-lg bg-amber-400 px-4 py-2 text-sm font-semibold text-gray-950 transition hover:bg-amber-300">
                    Save Sharing Preferences
                </button>
            </form>
        </div>
    @endif
</div>
