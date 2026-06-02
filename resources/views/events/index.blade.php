<x-app-layout>
    @php
        $viewRoutes = [
            'month' => 'calendar.month',
            'week' => 'calendar.week',
            'work-week' => 'calendar.week',
            'day' => 'calendar.day',
            'agenda' => 'calendar.agenda',
        ];
        $viewLabels = [
            'month' => 'Month',
            'week' => 'Week',
            'day' => 'Day',
            'agenda' => 'Agenda',
        ];
        $chip = fn ($active = false) => 'inline-flex items-center rounded-full px-3 py-1.5 text-xs font-semibold transition '.($active ? 'bg-[#0B1F3A] text-white' : 'border border-[#C8A24A]/50 bg-[#FFF8E5] text-[#0B1F3A] hover:bg-[#F7E8B8]');
        $panel = 'rounded-lg border border-[#516070] bg-white/90 shadow-sm';
        $eventCard = 'rounded-md border px-2.5 py-2 text-left shadow-sm transition hover:shadow-md';
    @endphp

    <div class="space-y-5" x-data="{ createOpen: @js($errors->any()), allDay: @js((bool) old('is_all_day')), recurring: @js((bool) old('is_recurring')), recurrenceEndType: @js(old('recurrence_end_type', 'never')), recurrenceFrequency: @js(old('recurrence_frequency', 'weekly')) }" x-on:keydown.escape.window="createOpen = false">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-xl border border-[#516070] bg-gradient-to-r from-[#0B1F3A] via-[#132B4B] to-[#0B1F3A] p-5 text-white shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-[#C8A24A]">Calendar & Events</p>
                    <h1 class="mt-1 text-2xl font-semibold">EFGTrack Calendar</h1>
                    <p class="mt-1 max-w-3xl text-sm text-slate-200">
                        Coordinate team trainings, mentor sessions, prospect appointments, rank reviews, licensing deadlines, and organization-wide events.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('calendar.index', ['date' => now()->toDateString(), 'view' => $viewMode]) }}" class="{{ $chip() }}">Today</a>
                    <a href="{{ route($viewRoutes[$viewMode] ?? 'calendar.index', array_merge(request()->query(), ['date' => $previousDate])) }}" class="{{ $chip() }}" aria-label="Previous period">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1-.02 1.06L9.06 10l3.71 3.71a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06.02Z" clip-rule="evenodd" /></svg>
                    </a>
                    <a href="{{ route($viewRoutes[$viewMode] ?? 'calendar.index', array_merge(request()->query(), ['date' => $nextDate])) }}" class="{{ $chip() }}" aria-label="Next period">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" /></svg>
                    </a>
                    <a href="{{ route('calendar.export', request()->query()) }}" class="{{ $chip() }}">Export</a>
                    <a href="{{ route('calendar.settings') }}" class="{{ $chip() }}">Settings</a>
                </div>
            </div>
        </section>

        <section class="grid gap-4 xl:grid-cols-[17rem_minmax(0,1fr)_20rem]">
            <aside class="{{ $panel }} overflow-hidden">
                <div class="border-b border-[#516070]/20 bg-[#F6F8FB] px-4 py-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-[#C8A24A]">Create</p>
                            <h2 class="text-sm font-semibold text-[#0B1F3A]">Schedule Event</h2>
                        </div>
                        <button type="button" x-on:click="createOpen = true" class="flex h-9 w-9 items-center justify-center rounded-full bg-[#C8A24A] text-[#0B1F3A] shadow-sm transition hover:bg-[#D8B75F]" title="Create event">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" /></svg>
                        </button>
                    </div>
                </div>

                <div class="space-y-4 p-4">
                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <div class="text-sm font-semibold text-[#0B1F3A]">{{ $currentDate->format('F Y') }}</div>
                            <div class="text-xs font-semibold text-slate-500">{{ $viewLabels[$viewMode] ?? 'Month' }}</div>
                        </div>
                        <div class="grid grid-cols-7 text-center text-[0.65rem] font-bold uppercase text-slate-400">
                            @foreach (['M','T','W','T','F','S','S'] as $dayName)
                                <span class="py-1">{{ $dayName }}</span>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-7 gap-1 text-center text-xs">
                            @foreach ($miniCalendarDays as $day)
                                <a
                                    href="{{ route('calendar.day', array_merge(request()->query(), ['date' => $day->toDateString()])) }}"
                                    class="rounded-md py-1.5 font-semibold transition {{ $day->isSameDay($currentDate) ? 'bg-[#C8A24A] text-[#0B1F3A]' : ($day->month === $currentDate->month ? 'text-[#0B1F3A] hover:bg-[#FFF8E5]' : 'text-slate-300 hover:bg-slate-50') }}"
                                >
                                    {{ $day->day }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-2">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Views</p>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($viewLabels as $key => $label)
                                <a href="{{ route($viewRoutes[$key] ?? 'calendar.index', array_merge(request()->query(), ['view' => $key, 'date' => $currentDate->toDateString()])) }}" class="{{ $chip($viewMode === $key) }} justify-center">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <form method="GET" action="{{ route('calendar.index') }}" class="space-y-3">
                        <input type="hidden" name="view" value="{{ $viewMode }}">
                        <input type="hidden" name="date" value="{{ $currentDate->toDateString() }}">

                        <div>
                            <label for="calendar-search" class="text-xs font-bold uppercase tracking-wide text-slate-500">Search</label>
                            <input id="calendar-search" name="q" value="{{ $filters['q'] ?? '' }}" class="mt-1 w-full rounded-md border-[#516070]/30 bg-white text-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]" placeholder="Event, place, person">
                        </div>

                        <div>
                            <label for="calendar-type" class="text-xs font-bold uppercase tracking-wide text-slate-500">Type</label>
                            <select id="calendar-type" name="type" class="mt-1 w-full rounded-md border-[#516070]/30 bg-white text-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                                <option value="">All event types</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}" @selected(($filters['type'] ?? '') == $type->id)>{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button class="w-full rounded-md bg-[#0B1F3A] px-3 py-2 text-sm font-semibold text-white transition hover:bg-[#132B4B]">Apply Filters</button>
                    </form>

                    <div class="space-y-2">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">My Calendars</p>
                        <form id="calendar-filter-form" method="GET" action="{{ route('calendar.index') }}" class="hidden">
                            <input type="hidden" name="calendars_filter" value="1">
                            <input type="hidden" name="view" value="{{ $viewMode }}">
                            <input type="hidden" name="date" value="{{ $currentDate->toDateString() }}">
                            @foreach (['q', 'type', 'status', 'visibility'] as $filterKey)
                                @if (filled($filters[$filterKey] ?? null))
                                    <input type="hidden" name="{{ $filterKey }}" value="{{ $filters[$filterKey] }}">
                                @endif
                            @endforeach
                        </form>

                        <div class="space-y-2">
                            @foreach ($categories as $category)
                                <div x-data="{ editOpen: false, color: @js(old('color', $category->color)) }" class="relative flex items-center justify-between rounded-md border border-[#516070]/20 bg-[#F8FAFC] px-3 py-2">
                                    <label class="flex min-w-0 flex-1 items-center gap-2 text-sm font-medium text-[#0B1F3A]">
                                        <input
                                            type="checkbox"
                                            form="calendar-filter-form"
                                            name="category_ids[]"
                                            value="{{ $category->id }}"
                                            class="rounded border-[#516070] text-[#C8A24A] focus:ring-[#C8A24A]"
                                            @checked(in_array($category->id, $selectedCalendarIds, true))
                                            x-on:change="$el.form.submit()"
                                        >
                                        <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background-color: {{ $category->color }}"></span>
                                        <span class="truncate">{{ $category->name }}</span>
                                    </label>

                                    <div class="ml-2 flex items-center gap-1">
                                        <span class="mr-1 text-xs font-semibold text-slate-500">{{ $events->where('calendar_category_id', $category->id)->count() }}</span>

                                        @can('manage organization calendar')
                                            <button type="button" x-on:click="editOpen = true" title="Customize calendar" class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-[#C8A24A]/40 bg-[#FFF9EA] text-[#0B1F3A] transition hover:bg-[#F7E8B8]">
                                                <span class="sr-only">Customize {{ $category->name }}</span>
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="m13.586 3.586 2.828 2.828-9.193 9.193a2 2 0 0 1-.878.51l-3.03.757a.75.75 0 0 1-.91-.91l.757-3.03a2 2 0 0 1 .51-.878l9.193-9.193ZM15 2.172a2 2 0 0 1 2.828 0 2 2 0 0 1 0 2.828l-.414.414-2.828-2.828.414-.414Z" />
                                                </svg>
                                            </button>

                                            <form method="POST" action="{{ route('calendar.categories.destroy', $category) }}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="return_to" value="{{ url()->full() }}">
                                                <button type="submit" title="Delete calendar" class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-red-200 bg-red-50 text-red-600 transition hover:bg-red-100" onclick="return confirm('Delete this calendar category? Existing events will remain, but this calendar group will be hidden.')">
                                                    <span class="sr-only">Delete {{ $category->name }}</span>
                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75V4.5H3.75a.75.75 0 0 0 0 1.5h.32l.67 10.03A2.75 2.75 0 0 0 7.48 18h5.04a2.75 2.75 0 0 0 2.74-1.97L15.93 6h.32a.75.75 0 0 0 0-1.5H14v-.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM7.5 4.5v-.75c0-.69.56-1.25 1.25-1.25h2.5c.69 0 1.25.56 1.25 1.25v.75h-5Z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>

                                    @can('manage organization calendar')
                                        <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4">
                                            <div class="w-full max-w-md rounded-xl border border-[#C8A24A]/50 bg-white shadow-xl" x-on:click.outside="editOpen = false">
                                                <form method="POST" action="{{ route('calendar.categories.update', $category) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="return_to" value="{{ url()->full() }}">
                                                    <div class="border-b border-slate-100 px-5 py-4">
                                                        <div class="flex items-center justify-between gap-3">
                                                            <div>
                                                                <p class="text-xs font-bold uppercase tracking-wide text-[#C8A24A]">Customize Calendar</p>
                                                                <h3 class="text-lg font-semibold text-[#0B1F3A]">{{ $category->name }}</h3>
                                                            </div>
                                                            <button type="button" x-on:click="editOpen = false" class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200" aria-label="Close customize modal">
                                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" /></svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="space-y-4 px-5 py-5">
                                                        <div>
                                                            <label for="calendar-name-{{ $category->id }}" class="block text-sm font-semibold text-[#0B1F3A]">Name</label>
                                                            <input id="calendar-name-{{ $category->id }}" name="name" value="{{ old('name', $category->name) }}" class="mt-2 block w-full rounded-lg border-[#516070]/40 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                                                        </div>
                                                        <div>
                                                            <label for="calendar-color-{{ $category->id }}" class="block text-sm font-semibold text-[#0B1F3A]">Color</label>
                                                            <div class="mt-2 flex items-center gap-3">
                                                                <input id="calendar-color-{{ $category->id }}" type="color" x-model="color" class="h-10 w-14 rounded border border-[#516070]/40 bg-white p-1">
                                                                <input name="color" x-model="color" class="block w-full rounded-lg border-[#516070]/40 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]" placeholder="#C8A24A">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex justify-end gap-3 border-t border-slate-100 px-5 py-4">
                                                        <button type="button" x-on:click="editOpen = false" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                                                        <button type="submit" class="rounded-lg bg-[#C8A24A] px-4 py-2 text-sm font-bold text-[#0B1F3A] hover:bg-[#D8B75F]">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endcan
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>

            <main class="{{ $panel }} min-w-0 overflow-hidden">
                <div class="border-b border-[#516070]/20 bg-[#F6F8FB] px-4 py-3">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-[#C8A24A]">{{ $viewLabels[$viewMode] ?? 'Month' }} View</p>
                            <h2 class="text-xl font-semibold text-[#0B1F3A]">
                                @if ($viewMode === 'day')
                                    {{ $currentDate->format('l, F j, Y') }}
                                @elseif ($viewMode === 'agenda')
                                    {{ $rangeStart->format('M j') }} - {{ $rangeEnd->format('M j, Y') }}
                                @elseif (in_array($viewMode, ['week', 'work-week'], true))
                                    {{ $rangeStart->format('M j') }} - {{ $rangeEnd->format('M j, Y') }}
                                @else
                                    {{ $currentDate->format('F Y') }}
                                @endif
                            </h2>
                        </div>
                        <div class="grid grid-cols-4 gap-2 text-center">
                            @foreach ([['Visible', $stats['visible']], ['Upcoming', $stats['upcoming']], ['Training', $stats['training']], ['Prospects', $stats['prospects']]] as [$label, $value])
                                <div class="rounded-md border border-[#C8A24A]/40 bg-[#FFF9EA] px-3 py-2">
                                    <div class="text-sm font-bold text-[#C8A24A]">{{ $value }}</div>
                                    <div class="text-[0.65rem] font-semibold uppercase text-slate-500">{{ $label }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if ($viewMode === 'agenda')
                    <div class="divide-y divide-[#516070]/15">
                        @forelse ($events as $event)
                            <a href="{{ route('calendar.events.show', $event) }}" class="grid gap-3 px-4 py-3 transition hover:shadow-sm sm:grid-cols-[7rem_minmax(0,1fr)_8rem]" style="background-color: {{ $event->display_color }}12; border-left: 4px solid {{ $event->display_color }};">
                                <div class="text-sm font-semibold text-[#0B1F3A]">
                                    {{ $event->starts_at->format('M j') }}
                                    <div class="text-xs font-medium text-slate-500">{{ $event->is_all_day ? 'All day' : $event->starts_at->format('g:i A') }}</div>
                                </div>
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-[#0B1F3A]">{{ $event->title }}</div>
                                    <div class="mt-1 line-clamp-2 text-xs text-slate-500">{{ $event->description ?: 'No description added yet.' }}</div>
                                </div>
                                <div class="flex items-start justify-end">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold text-[#0B1F3A]" style="background-color: {{ $event->display_color }}33; border: 1px solid {{ $event->display_color }}">
                                        {{ $event->type?->name ?? 'Event' }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-10 text-center text-sm text-slate-500">No visible calendar events in this range.</div>
                        @endforelse
                    </div>
                @elseif ($viewMode === 'day')
                    <div class="overflow-x-auto">
                        <div class="min-w-[42rem] divide-y divide-[#516070]/15">
                            @foreach ($hours as $hour)
                                @php
                                    $hourEvents = $events->filter(fn ($event) => (int) $event->starts_at->format('G') === $hour);
                                @endphp
                                <div class="grid grid-cols-[5rem_minmax(0,1fr)]">
                                    <div class="bg-[#F8FAFC] px-3 py-4 text-xs font-semibold text-slate-500">{{ sprintf('%02d:00', $hour) }}</div>
                                    <div class="min-h-20 space-y-2 px-3 py-3">
                                        @foreach ($hourEvents as $event)
                                            <a href="{{ route('calendar.events.show', $event) }}" class="{{ $eventCard }} block" style="background-color: {{ $event->display_color }}1A; border-color: {{ $event->display_color }};">
                                                <div class="flex items-center gap-2">
                                                    <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $event->display_color }}"></span>
                                                    <span class="truncate text-sm font-semibold text-[#0B1F3A]">{{ $event->title }}</span>
                                                </div>
                                                <div class="mt-1 text-xs text-slate-500">{{ $event->starts_at->format('g:i A') }}{{ $event->ends_at ? ' - '.$event->ends_at->format('g:i A') : '' }}</div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif (in_array($viewMode, ['week', 'work-week'], true))
                    <div class="overflow-x-auto">
                        <div class="min-w-[52rem]">
                            <div class="grid border-b border-[#516070]/15" style="grid-template-columns: repeat({{ $weekDays->count() }}, minmax(0, 1fr));">
                                @foreach ($weekDays as $day)
                                    <a href="{{ route('calendar.day', array_merge(request()->query(), ['date' => $day->toDateString()])) }}" class="border-r border-[#516070]/15 bg-[#F8FAFC] px-3 py-3 text-center last:border-r-0">
                                        <div class="text-xs font-bold uppercase text-slate-500">{{ $day->format('D') }}</div>
                                        <div class="mt-1 text-lg font-semibold {{ $day->isToday() ? 'text-[#C8A24A]' : 'text-[#0B1F3A]' }}">{{ $day->day }}</div>
                                    </a>
                                @endforeach
                            </div>
                            <div class="grid min-h-[34rem]" style="grid-template-columns: repeat({{ $weekDays->count() }}, minmax(0, 1fr));">
                                @foreach ($weekDays as $day)
                                    <div class="space-y-2 border-r border-[#516070]/15 p-2 last:border-r-0">
                                        @foreach ($eventsByDate->get($day->toDateString(), collect()) as $event)
                                            <a href="{{ route('calendar.events.show', $event) }}" class="{{ $eventCard }} block" style="background-color: {{ $event->display_color }}1A; border-color: {{ $event->display_color }};">
                                                <div class="truncate text-xs font-bold text-[#C8A24A]">{{ $event->is_all_day ? 'All day' : $event->starts_at->format('g:i A') }}</div>
                                                <div class="truncate text-sm font-semibold text-[#0B1F3A]">{{ $event->title }}</div>
                                                <div class="mt-1 truncate text-xs text-slate-500">{{ $event->type?->name ?? 'Event' }}</div>
                                            </a>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <div class="min-w-[54rem]">
                            <div class="grid grid-cols-7 border-b border-[#516070]/15 bg-[#F8FAFC] text-center text-xs font-bold uppercase text-slate-500">
                                @foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $dayName)
                                    <div class="border-r border-[#516070]/15 px-3 py-2 last:border-r-0">{{ $dayName }}</div>
                                @endforeach
                            </div>
                            <div class="grid grid-cols-7">
                                @foreach ($monthDays as $day)
                                    <div class="min-h-36 border-b border-r border-[#516070]/15 bg-white p-2 last:border-r-0 {{ $day->month === $currentDate->month ? '' : 'bg-slate-50/70' }}">
                                        <div class="mb-2 flex items-center justify-between">
                                            <a href="{{ route('calendar.day', array_merge(request()->query(), ['date' => $day->toDateString()])) }}" class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold {{ $day->isToday() ? 'bg-[#C8A24A] text-[#0B1F3A]' : 'text-slate-600 hover:bg-[#FFF8E5]' }}">
                                                {{ $day->day }}
                                            </a>
                                            <span class="text-[0.65rem] font-semibold text-slate-400">{{ $eventsByDate->get($day->toDateString(), collect())->count() }}</span>
                                        </div>
                                        <div class="space-y-1.5">
                                            @foreach ($eventsByDate->get($day->toDateString(), collect())->take(4) as $event)
                                                <a href="{{ route('calendar.events.show', $event) }}" class="block rounded-md border px-2 py-1 text-xs font-semibold text-[#0B1F3A] hover:shadow-sm" style="background-color: {{ $event->display_color }}1A; border-color: {{ $event->display_color }};">
                                                    <span class="mr-1 inline-block h-2 w-2 rounded-full" style="background-color: {{ $event->display_color }}"></span>
                                                    {{ $event->is_all_day ? '' : $event->starts_at->format('g:i').' ' }}{{ str($event->title)->limit(26) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </main>

            <aside class="{{ $panel }} overflow-hidden">
                <div class="border-b border-[#516070]/20 bg-[#F6F8FB] px-4 py-3">
                    <p class="text-xs font-bold uppercase tracking-wide text-[#C8A24A]">Details</p>
                    <h2 class="text-sm font-semibold text-[#0B1F3A]">Upcoming Events</h2>
                </div>

                <div class="space-y-3 p-4">
                    @forelse ($upcomingEvents as $event)
                        <a href="{{ route('calendar.events.show', $event) }}" class="block rounded-lg border p-3 transition hover:shadow-sm" style="background-color: {{ $event->display_color }}12; border-color: {{ $event->display_color }};">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold text-[#0B1F3A]">{{ $event->title }}</div>
                                    <div class="mt-1 text-xs font-medium text-slate-500">{{ $event->starts_at->format('M j, g:i A') }}</div>
                                </div>
                                <span class="h-3 w-3 rounded-full" style="background-color: {{ $event->display_color }}"></span>
                            </div>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <span class="rounded-full bg-white px-2 py-1 text-[0.65rem] font-semibold text-slate-600">{{ $event->type?->name ?? 'Event' }}</span>
                                <span class="rounded-full bg-white px-2 py-1 text-[0.65rem] font-semibold text-slate-600">{{ str($event->visibility)->headline() }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-lg border border-dashed border-[#516070]/30 bg-[#F8FAFC] px-4 py-6 text-sm text-slate-500">
                            Nothing upcoming yet.
                        </div>
                    @endforelse
                </div>
            </aside>
        </section>

        <button type="button" x-on:click="createOpen = true" class="fixed bottom-6 right-6 flex h-14 w-14 items-center justify-center rounded-full bg-[#C8A24A] text-[#0B1F3A] shadow-lg ring-4 ring-white lg:hidden" title="Create event">
            <svg class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" /></svg>
        </button>

        @include('events.partials.create-event-modal')
    </div>
</x-app-layout>
