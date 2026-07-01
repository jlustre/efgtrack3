@php
    $panels = $activity ?? [];
    $panelOrder = [
        'tasks_due_today',
        'upcoming_meetings',
        'calendar',
        'notifications',
        'recent_messages',
    ];
@endphp

<section>
    <div class="mb-4">
        <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Daily Activity</p>
        <h2 class="text-lg font-semibold text-[#0B1F3A]">Tasks, Meetings & Communications</h2>
        <p class="mt-1 text-sm text-slate-500">Live tasks, meetings, calendar events, notifications, and messages from your account.</p>
    </div>

    <div class="grid auto-rows-fr gap-4 md:grid-cols-2 xl:grid-cols-2">
        @foreach ($panelOrder as $panelKey)
            @if (isset($panels[$panelKey]))
                <x-dashboard-activity-panel :panel="$panels[$panelKey]" />
            @endif
        @endforeach
    </div>
</section>
