<x-app-layout>
    <div class="mx-auto max-w-5xl space-y-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('calendar.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 transition hover:text-[#C8A24A]">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m15 18-6-6 6-6" /></svg>
                    Back to Calendar
                </a>
                <p class="mt-2 text-xs font-bold uppercase tracking-wide text-[#C8A24A]">Calendar Settings</p>
                <h1 class="text-2xl font-semibold text-[#0B1F3A]">My Availability & Schedule Blocks</h1>
                <p class="mt-1 max-w-2xl text-sm text-slate-600">
                    Block recurring weekly times for work, personal activities, and other commitments. Your CFM can see shared blocks to coordinate mentor sessions.
                </p>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <section id="schedule-sharing" class="rounded-lg border border-[#516070] bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-[#0B1F3A]">Share With Your CFM</h2>
            <p class="mt-1 text-sm text-slate-600">When enabled, your assigned CFM can see your blocked times on their calendar to avoid scheduling conflicts.</p>
            <form method="POST" action="{{ route('calendar.schedule-sharing.update') }}" class="mt-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="return_to" value="{{ route('calendar.settings') }}">
                <label class="flex items-start gap-3 rounded-lg border border-[#C8A24A]/30 bg-[#FFF9EA] px-4 py-3">
                    <input
                        type="checkbox"
                        name="share_schedule_blocks_with_mentor"
                        value="1"
                        class="mt-1 rounded border-[#516070] text-[#C8A24A] focus:ring-[#C8A24A]"
                        @checked(old('share_schedule_blocks_with_mentor', $preference->share_schedule_blocks_with_mentor ?? true))
                        onchange="this.form.submit()"
                    >
                    <span>
                        <span class="font-semibold text-[#0B1F3A]">Share my blocked times with my CFM</span>
                        <span class="mt-1 block text-xs text-slate-500">CFMs who share their calendar with you will also appear on your calendar view.</span>
                    </span>
                </label>
            </form>
        </section>

        <section id="weekly-blocks" class="rounded-lg border border-[#516070] bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-[#0B1F3A]">Weekly Recurring Blocks</h2>
                    <p class="mt-1 text-sm text-slate-600">Set repeating unavailable windows for each day of the week.</p>
                </div>
            </div>

            @if ($weeklyBlocks->isNotEmpty())
                <div class="mt-4 space-y-2">
                    @foreach ($weeklyBlocks->groupBy('weekday') as $weekday => $blocks)
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $weekdayLabels[$weekday] ?? 'Day '.$weekday }}</p>
                            <div class="mt-2 space-y-2">
                                @foreach ($blocks as $block)
                                    <div class="flex flex-col gap-2 rounded-md border border-white bg-white px-3 py-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <span class="h-3 w-3 shrink-0 rounded-full" style="background-color: {{ $block->typeColor() }}"></span>
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-[#0B1F3A]">{{ $block->displayLabel() }}</p>
                                                <p class="text-xs text-slate-500">
                                                    {{ substr((string) $block->starts_at, 0, 5) }} – {{ substr((string) $block->ends_at, 0, 5) }}
                                                    · {{ $block->typeLabel() }}
                                                    @if ($block->is_shared)
                                                        · Shared
                                                    @else
                                                        · Private
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <form method="POST" action="{{ route('calendar.schedule-blocks.destroy', $block) }}" class="shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="return_to" value="{{ route('calendar.settings') }}">
                                            <button type="submit" class="rounded-md border border-red-200 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-50" onclick="return confirm('Remove this weekly block?')">Remove</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-4 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-sm text-slate-500">
                    No weekly blocks yet. Add your work schedule or personal time below.
                </p>
            @endif

            <form method="POST" action="{{ route('calendar.schedule-blocks.store') }}" class="mt-5 space-y-4 rounded-lg border border-[#C8A24A]/30 bg-[#FFF9EA] p-4">
                @csrf
                <input type="hidden" name="return_to" value="{{ route('calendar.settings') }}">
                <p class="text-xs font-bold uppercase tracking-wide text-[#8A6A1F]">Add Weekly Block</p>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label for="block_type" class="block text-xs font-semibold text-[#0B1F3A]">Type</label>
                        <select id="block_type" name="block_type" required class="mt-1 w-full rounded-md border-[#516070]/30 text-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                            @foreach ($blockTypes as $key => $type)
                                <option value="{{ $key }}" @selected(old('block_type') === $key)>{{ $type['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="weekday" class="block text-xs font-semibold text-[#0B1F3A]">Day</label>
                        <select id="weekday" name="weekday" required class="mt-1 w-full rounded-md border-[#516070]/30 text-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                            @foreach ($weekdayLabels as $value => $label)
                                <option value="{{ $value }}" @selected((int) old('weekday') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="label" class="block text-xs font-semibold text-[#0B1F3A]">Label (optional)</label>
                        <input id="label" name="label" value="{{ old('label') }}" placeholder="e.g. Day job, Gym" class="mt-1 w-full rounded-md border-[#516070]/30 text-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    </div>
                    <div>
                        <label for="starts_at" class="block text-xs font-semibold text-[#0B1F3A]">Starts</label>
                        <input id="starts_at" type="time" name="starts_at" value="{{ old('starts_at', '09:00') }}" required class="mt-1 w-full rounded-md border-[#516070]/30 text-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    </div>
                    <div>
                        <label for="ends_at" class="block text-xs font-semibold text-[#0B1F3A]">Ends</label>
                        <input id="ends_at" type="time" name="ends_at" value="{{ old('ends_at', '17:00') }}" required class="mt-1 w-full rounded-md border-[#516070]/30 text-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 text-sm text-[#0B1F3A]">
                            <input type="checkbox" name="is_shared" value="1" class="rounded border-[#516070] text-[#C8A24A] focus:ring-[#C8A24A]" @checked(old('is_shared', '1'))>
                            Share with CFM
                        </label>
                    </div>
                </div>
                @if ($errors->any())
                    <div class="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <button type="submit" class="rounded-lg bg-[#C8A24A] px-4 py-2 text-sm font-bold text-[#0B1F3A] hover:bg-[#D8B75F]">Add Weekly Block</button>
            </form>
        </section>

        <section id="date-blocks" class="rounded-lg border border-[#516070] bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-[#0B1F3A]">One-Time Date Blocks</h2>
            <p class="mt-1 text-sm text-slate-600">Block specific dates for vacations, appointments, or exceptions to your weekly schedule.</p>

            @if ($blockOverrides->isNotEmpty())
                <div class="mt-4 space-y-2">
                    @foreach ($blockOverrides as $override)
                        <div class="flex flex-col gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex min-w-0 items-center gap-3">
                                <span class="h-3 w-3 shrink-0 rounded-full" style="background-color: {{ $override->typeColor() }}"></span>
                                <div>
                                    <p class="text-sm font-semibold text-[#0B1F3A]">{{ $override->displayLabel() }}</p>
                                    <p class="text-xs text-slate-500">
                                        {{ $override->block_date->format('M j, Y') }}
                                        @if ($override->is_all_day)
                                            · All day
                                        @else
                                            · {{ substr((string) $override->starts_at, 0, 5) }} – {{ substr((string) $override->ends_at, 0, 5) }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('calendar.schedule-block-overrides.destroy', $override) }}">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="return_to" value="{{ route('calendar.settings') }}">
                                <button type="submit" class="rounded-md border border-red-200 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-50" onclick="return confirm('Remove this date block?')">Remove</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('calendar.schedule-block-overrides.store') }}" class="mt-5 space-y-4 rounded-lg border border-slate-200 bg-slate-50 p-4" x-data="{ allDay: @js((bool) old('is_all_day')) }">
                @csrf
                <input type="hidden" name="return_to" value="{{ route('calendar.settings') }}">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Add Date Block</p>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label for="block_date" class="block text-xs font-semibold text-[#0B1F3A]">Date</label>
                        <input id="block_date" type="date" name="block_date" value="{{ old('block_date') }}" required class="mt-1 w-full rounded-md border-[#516070]/30 text-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    </div>
                    <div>
                        <label for="override_block_type" class="block text-xs font-semibold text-[#0B1F3A]">Type</label>
                        <select id="override_block_type" name="block_type" required class="mt-1 w-full rounded-md border-[#516070]/30 text-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                            @foreach ($blockTypes as $key => $type)
                                <option value="{{ $key }}">{{ $type['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="override_label" class="block text-xs font-semibold text-[#0B1F3A]">Label</label>
                        <input id="override_label" name="label" value="{{ old('label') }}" placeholder="e.g. Vacation" class="mt-1 w-full rounded-md border-[#516070]/30 text-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    </div>
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="flex items-center gap-2 text-sm text-[#0B1F3A]">
                            <input type="checkbox" name="is_all_day" value="1" x-model="allDay" class="rounded border-[#516070] text-[#C8A24A] focus:ring-[#C8A24A]">
                            All day
                        </label>
                    </div>
                    <div x-show="!allDay" x-cloak>
                        <label for="override_starts_at" class="block text-xs font-semibold text-[#0B1F3A]">Starts</label>
                        <input id="override_starts_at" type="time" name="starts_at" value="{{ old('starts_at', '09:00') }}" class="mt-1 w-full rounded-md border-[#516070]/30 text-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    </div>
                    <div x-show="!allDay" x-cloak>
                        <label for="override_ends_at" class="block text-xs font-semibold text-[#0B1F3A]">Ends</label>
                        <input id="override_ends_at" type="time" name="ends_at" value="{{ old('ends_at', '17:00') }}" class="mt-1 w-full rounded-md border-[#516070]/30 text-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    </div>
                </div>
                <button type="submit" class="rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#132B4B]">Add Date Block</button>
            </form>
        </section>

        <section class="rounded-lg border border-[#516070] bg-white p-5 shadow-sm">
            <h2 class="text-sm font-semibold text-[#0B1F3A]">General Preferences</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-[#516070]/20 bg-[#F8FAFC] p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Default View</p>
                    <p class="mt-1 text-lg font-semibold text-[#0B1F3A]">{{ str($preference->default_view)->headline() }}</p>
                </div>
                <div class="rounded-lg border border-[#516070]/20 bg-[#F8FAFC] p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Timezone</p>
                    <p class="mt-1 text-lg font-semibold text-[#0B1F3A]">{{ $preference->timezone }}</p>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
