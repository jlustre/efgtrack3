<?php

namespace App\Http\Controllers;

use App\Models\CalendarCategory;
use App\Models\CalendarEvent;
use App\Models\CalendarEventActivityLog;
use App\Models\CalendarEventAttendee;
use App\Models\CalendarEventNote;
use App\Models\CalendarEventRecurrence;
use App\Models\CalendarEventReminder;
use App\Models\CalendarEventType;
use App\Models\User;
use App\Models\UserCalendarPreference;
use App\Services\CalendarShareService;
use App\Support\LocationOptions;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __construct(private readonly CalendarShareService $calendarShare) {}

    public function index(Request $request): View
    {
        return $this->calendar($request, $request->string('view', 'month')->value());
    }

    public function month(Request $request): View
    {
        return $this->calendar($request, 'month');
    }

    public function week(Request $request): View
    {
        return $this->calendar($request, 'week');
    }

    public function day(Request $request): View
    {
        return $this->calendar($request, 'day');
    }

    public function agenda(Request $request): View
    {
        return $this->calendar($request, 'agenda');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermissionTo('create calendar events'), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'calendar_event_type_id' => ['nullable', 'integer', 'exists:calendar_event_types,id'],
            'calendar_category_id' => ['nullable', 'integer', 'exists:calendar_categories,id'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'timezone' => ['required', 'string', Rule::in(array_keys(LocationOptions::timezones()))],
            'is_all_day' => ['nullable', 'boolean'],
            'is_recurring' => ['nullable', 'boolean'],
            'recurrence_frequency' => ['nullable', Rule::in(['daily', 'weekly', 'monthly', 'yearly'])],
            'recurrence_interval' => ['nullable', 'integer', 'min:1', 'max:12'],
            'recurrence_weekdays' => ['nullable', 'array'],
            'recurrence_weekdays.*' => ['string', Rule::in(['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU'])],
            'recurrence_end_type' => ['nullable', Rule::in(['never', 'after', 'on'])],
            'recurrence_ends_after_occurrences' => ['nullable', 'integer', 'min:1', 'max:120'],
            'recurrence_ends_on' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'location' => ['nullable', 'string', 'max:255'],
            'meeting_link' => ['nullable', 'url', 'max:255'],
            'visibility' => ['required', Rule::in(['private', 'shared_team', 'shared_downline', 'public_organization'])],
            'status' => ['required', Rule::in(['scheduled', 'draft'])],
            'notes' => ['nullable', 'string'],
            'attendee_user_ids' => ['nullable', 'array'],
            'attendee_user_ids.*' => ['integer', 'exists:users,id'],
            'external_attendee_name' => ['nullable', 'string', 'max:255'],
            'external_attendee_email' => ['nullable', 'email', 'max:255'],
            'reminder_minutes' => ['nullable', 'array'],
            'reminder_minutes.*' => ['integer', 'min:0', 'max:43200'],
            'reminder_channel' => ['required', Rule::in(['in_app', 'email', 'both'])],
            'return_to' => ['nullable', 'string', 'max:2048'],
        ]);

        $type = filled($validated['calendar_event_type_id'] ?? null)
            ? CalendarEventType::query()->find($validated['calendar_event_type_id'])
            : null;
        $category = filled($validated['calendar_category_id'] ?? null)
            ? CalendarCategory::query()->find($validated['calendar_category_id'])
            : null;

        if ($category && ! $this->assignableCategories($request->user())->contains('id', $category->id)) {
            return back()
                ->withErrors(['calendar_category_id' => 'Select a calendar category you are allowed to use.'])
                ->withInput();
        }

        $resolvedCategory = $category ?? $type?->category;
        $categoryId = $resolvedCategory?->id;
        $color = $resolvedCategory?->color ?? $type?->color ?? '#C8A24A';
        $isRecurring = (bool) ($validated['is_recurring'] ?? false);
        $recurrence = $isRecurring ? $this->recurrenceData($validated) : null;

        $event = CalendarEvent::create([
            'calendar_event_type_id' => $type?->id,
            'calendar_category_id' => $categoryId,
            'organizer_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'] ?? null,
            'timezone' => $validated['timezone'],
            'is_all_day' => (bool) ($validated['is_all_day'] ?? false),
            'is_recurring' => $isRecurring,
            'recurrence_rule' => $recurrence['rule'] ?? null,
            'location' => $validated['location'] ?? null,
            'meeting_link' => $validated['meeting_link'] ?? null,
            'visibility' => $validated['visibility'],
            'status' => $validated['status'],
            'color' => $color,
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($recurrence) {
            CalendarEventRecurrence::create([
                'calendar_event_id' => $event->id,
                'frequency' => $recurrence['frequency'],
                'interval' => $recurrence['interval'],
                'weekdays' => $recurrence['weekdays'],
                'ends_after_occurrences' => $recurrence['ends_after_occurrences'],
                'ends_on' => $recurrence['ends_on'],
            ]);
        }

        collect($validated['attendee_user_ids'] ?? [])
            ->unique()
            ->reject(fn (int|string $userId): bool => (int) $userId === $request->user()->id)
            ->each(fn (int|string $userId) => CalendarEventAttendee::create([
                'calendar_event_id' => $event->id,
                'user_id' => (int) $userId,
                'attendee_type' => 'user',
                'rsvp_status' => 'pending',
            ]));

        if (filled($validated['external_attendee_email'] ?? null) || filled($validated['external_attendee_name'] ?? null)) {
            CalendarEventAttendee::create([
                'calendar_event_id' => $event->id,
                'name' => $validated['external_attendee_name'] ?? null,
                'email' => $validated['external_attendee_email'] ?? null,
                'attendee_type' => 'external',
                'rsvp_status' => 'pending',
            ]);
        }

        foreach (array_unique(Arr::wrap($validated['reminder_minutes'] ?? [15])) as $minutesBefore) {
            CalendarEventReminder::create([
                'calendar_event_id' => $event->id,
                'user_id' => $request->user()->id,
                'minutes_before' => (int) $minutesBefore,
                'channel' => $validated['reminder_channel'],
            ]);
        }

        if (filled($validated['notes'] ?? null)) {
            CalendarEventNote::create([
                'calendar_event_id' => $event->id,
                'created_by' => $request->user()->id,
                'note' => $validated['notes'],
                'is_private' => false,
            ]);
        }

        CalendarEventActivityLog::create([
            'calendar_event_id' => $event->id,
            'user_id' => $request->user()->id,
            'action' => 'created',
            'payload' => [
                'visibility' => $event->visibility,
                'status' => $event->status,
                'attendee_count' => $event->attendees()->count(),
            ],
        ]);

        return redirect()
            ->to($this->safeReturnUrl($validated['return_to'] ?? route('calendar.index')))
            ->with('status', 'Calendar event created.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        abort_unless($request->user()->hasPermissionTo('view calendar'), 403);

        $user = $request->user();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('calendar_categories', 'name')->where(fn ($query) => $query->where('user_id', $user->id)),
            ],
            'color' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'is_public' => ['nullable', 'boolean'],
            'return_to' => ['nullable', 'string', 'max:2048'],
        ]);

        $slug = $this->uniqueCategorySlug($user->id, $validated['name']);

        $category = CalendarCategory::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'slug' => $slug,
            'color' => strtoupper($validated['color']),
            'icon' => 'calendar',
            'sort_order' => (int) (CalendarCategory::query()->where('user_id', $user->id)->max('sort_order') ?? 900) + 10,
            'is_active' => true,
            'is_public' => (bool) ($validated['is_public'] ?? false),
        ]);

        $preference = $this->calendarPreference($request);
        $visibleIds = collect($preference->visible_calendar_categories ?? [])
            ->push($category->id)
            ->unique()
            ->values()
            ->all();
        $preference->update(['visible_calendar_categories' => $visibleIds]);

        return redirect()
            ->to($this->safeReturnUrl($validated['return_to'] ?? route('calendar.index')))
            ->with('status', 'Personal calendar created.');
    }

    public function updateCategory(Request $request, CalendarCategory $category): RedirectResponse
    {
        abort_unless($this->canManageCategory($request->user(), $category), 403);

        $nameRule = $category->isSystem()
            ? Rule::unique('calendar_categories', 'name')->ignore($category->id)->where(fn ($query) => $query->whereNull('user_id'))
            : Rule::unique('calendar_categories', 'name')->ignore($category->id)->where(fn ($query) => $query->where('user_id', $category->user_id));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', $nameRule],
            'color' => ['required', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'is_public' => ['nullable', 'boolean'],
            'return_to' => ['nullable', 'string', 'max:2048'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'color' => strtoupper($validated['color']),
            'is_public' => $category->isSystem() ? false : (bool) ($validated['is_public'] ?? false),
        ]);

        return redirect()
            ->to($this->safeReturnUrl($validated['return_to'] ?? route('calendar.index')))
            ->with('status', 'Calendar updated.');
    }

    public function destroyCategory(Request $request, CalendarCategory $category): RedirectResponse
    {
        abort_unless($this->canManageCategory($request->user(), $category), 403);

        $category->delete();

        $preference = UserCalendarPreference::query()->where('user_id', $request->user()->id)->first();
        if ($preference) {
            $preference->update([
                'visible_calendar_categories' => collect($preference->visible_calendar_categories ?? [])
                    ->reject(fn ($id): bool => (int) $id === (int) $category->id)
                    ->values()
                    ->all(),
            ]);
        }

        return redirect()
            ->to($this->safeReturnUrl($request->input('return_to', route('calendar.index'))))
            ->with('status', 'Calendar deleted.');
    }

    public function show(Request $request, CalendarEvent $event): View
    {
        $this->authorize('view', $event);

        return view('events.show', [
            'event' => $event->load(['organizer', 'type', 'category', 'attendees.user', 'reminders', 'notes.creator']),
        ]);
    }

    public function settings(Request $request): View
    {
        $preference = UserCalendarPreference::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['timezone' => $request->user()->profile?->timezone ?? config('app.timezone')]
        );

        return view('events.settings', [
            'preference' => $preference,
            'categories' => $this->visibleCategories($request->user()),
        ]);
    }

    public function export(Request $request): Response
    {
        abort_unless($request->user()->hasAnyPermission(['view calendar', 'manage team calendar', 'manage organization calendar']), 403);

        $selectedCalendarIds = $this->selectedCalendarIds($request);

        $events = $this->visibleEventsQuery($request, $selectedCalendarIds)
            ->with(['organizer', 'type', 'category'])
            ->orderBy('starts_at')
            ->limit(1000)
            ->get();

        $csv = collect([['Title', 'Type', 'Category', 'Starts', 'Ends', 'Status', 'Visibility', 'Organizer', 'Location']])
            ->merge($events->map(fn (CalendarEvent $event): array => [
                $event->title,
                $event->type?->name,
                $event->category?->name,
                $event->starts_at?->toDateTimeString(),
                $event->ends_at?->toDateTimeString(),
                $event->status,
                $event->visibility,
                $event->organizer?->name,
                $event->location ?: $event->meeting_link,
            ]))
            ->map(fn (array $row): string => collect($row)->map(fn ($value): string => '"'.str_replace('"', '""', (string) $value).'"')->implode(','))
            ->implode("\n");

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="efgtrack-calendar-events.csv"',
        ]);
    }

    private function calendar(Request $request, string $viewMode): View
    {
        abort_unless($request->user()->hasAnyPermission(['view calendar', 'view shared calendar events', 'manage organization calendar']), 403);

        $viewMode = in_array($viewMode, ['month', 'week', 'work-week', 'day', 'agenda'], true) ? $viewMode : 'month';
        $currentDate = CarbonImmutable::parse($request->input('date', now()->toDateString()))->startOfDay();
        [$rangeStart, $rangeEnd] = $this->dateRange($currentDate, $viewMode);
        $selectedCalendarIds = $this->selectedCalendarIds($request);
        $sharedMentorCalendars = $this->calendarShare->sharedCfmOrganizersFor($request->user());

        $events = $this->visibleEventsQuery($request, $selectedCalendarIds)
            ->where(function (Builder $query) use ($rangeStart, $rangeEnd): void {
                $query->whereBetween('starts_at', [$rangeStart, $rangeEnd])
                    ->orWhereBetween('ends_at', [$rangeStart, $rangeEnd])
                    ->orWhere(function (Builder $query) use ($rangeStart, $rangeEnd): void {
                        $query->where('starts_at', '<=', $rangeStart)->where('ends_at', '>=', $rangeEnd);
                    });
            })
            ->with(['organizer:id,name,email', 'type', 'category', 'attendees'])
            ->orderBy('starts_at')
            ->get();

        $upcomingEvents = $this->visibleEventsQuery($request, $selectedCalendarIds)
            ->with(['type', 'category'])
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->limit(8)
            ->get();

        return view('events.index', [
            'viewMode' => $viewMode,
            'currentDate' => $currentDate,
            'rangeStart' => $rangeStart,
            'rangeEnd' => $rangeEnd,
            'events' => $events,
            'eventsByDate' => $events->groupBy(fn (CalendarEvent $event): string => $event->starts_at->toDateString()),
            'miniCalendarDays' => $this->miniCalendarDays($currentDate),
            'monthDays' => $this->monthDays($currentDate),
            'weekDays' => $this->weekDays($rangeStart, $viewMode === 'work-week'),
            'hours' => range(7, 20),
            'upcomingEvents' => $upcomingEvents,
            'categories' => $this->visibleCategories($request->user()),
            'assignableCategories' => $this->assignableCategories($request->user()),
            'types' => CalendarEventType::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'attendeeUsers' => $this->attendeeUsers($request),
            'eventTimezones' => LocationOptions::timezones(),
            'selectedCalendarIds' => $selectedCalendarIds,
            'sharedMentorCalendars' => $sharedMentorCalendars,
            'stats' => $this->stats($events, $upcomingEvents),
            'filters' => $request->only(['q', 'category', 'category_ids', 'calendars_filter', 'type', 'status', 'visibility']),
            'previousDate' => $this->shiftedDate($currentDate, $viewMode, -1),
            'nextDate' => $this->shiftedDate($currentDate, $viewMode, 1),
        ]);
    }

    private function visibleEventsQuery(Request $request, ?array $selectedCalendarIds = null): Builder
    {
        $user = $request->user();
        $selectedCalendarIds ??= $this->selectedCalendarIds($request);
        $sharedCfmOrganizerIds = $this->calendarShare->sharedCfmOrganizerIdsFor($user);

        return CalendarEvent::query()
            ->where(function (Builder $query): void {
                $query->whereNull('calendar_category_id')
                    ->orWhereHas('category', fn (Builder $query) => $query->where('is_active', true));
            })
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $search = trim((string) $request->string('q'));

                $query->where(function (Builder $query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), fn (Builder $query) => $query->where('calendar_category_id', $request->integer('category')))
            ->when(true, function (Builder $query) use ($selectedCalendarIds, $sharedCfmOrganizerIds): void {
                if (empty($selectedCalendarIds) && empty($sharedCfmOrganizerIds)) {
                    $query->whereRaw('1 = 0');

                    return;
                }

                $query->where(function (Builder $query) use ($selectedCalendarIds, $sharedCfmOrganizerIds): void {
                    if (! empty($selectedCalendarIds)) {
                        $query->whereIn('calendar_category_id', $selectedCalendarIds);
                    }

                    if (! empty($sharedCfmOrganizerIds)) {
                        $query->orWhere(function (Builder $query) use ($sharedCfmOrganizerIds): void {
                            $query->whereIn('organizer_id', $sharedCfmOrganizerIds)
                                ->where('visibility', '!=', 'private');
                        });
                    }
                });
            })
            ->when($request->filled('type'), fn (Builder $query) => $query->where('calendar_event_type_id', $request->integer('type')))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')))
            ->when($request->filled('visibility'), fn (Builder $query) => $query->where('visibility', $request->string('visibility')))
            ->where(function (Builder $query) use ($user, $sharedCfmOrganizerIds): void {
                if ($user->hasAnyPermission(['manage organization calendar', 'view private events'])) {
                    return;
                }

                $query->where('organizer_id', $user->id)
                    ->orWhereHas('attendees', fn (Builder $query) => $query->where('user_id', $user->id))
                    ->orWhere(function (Builder $query) use ($user): void {
                        $query->whereIn('visibility', ['public_organization', 'shared_downline'])
                            ->where(fn (Builder $query) => $query->where('visibility', 'public_organization')->orWhere('organizer_id', $user->sponsor_id));
                    })
                    ->orWhereHas('visibilityRules', function (Builder $query) use ($user): void {
                        $query->where('user_id', $user->id)
                            ->orWhere('team_id', $user->team_id)
                            ->orWhereIn('role_name', $user->getRoleNames()->all());
                    });

                if (! empty($sharedCfmOrganizerIds)) {
                    $query->orWhere(function (Builder $query) use ($sharedCfmOrganizerIds): void {
                        $query->whereIn('organizer_id', $sharedCfmOrganizerIds)
                            ->where('visibility', '!=', 'private');
                    });
                }
            });
    }

    private function attendeeUsers(Request $request): Collection
    {
        $user = $request->user();

        return User::query()
            ->where('id', '!=', $user->id)
            ->whereNull('deleted_at')
            ->when(
                ! $user->hasAnyPermission(['manage organization calendar', 'view all teams']),
                fn (Builder $query) => $query->where('team_id', $user->team_id)
            )
            ->orderBy('name')
            ->limit(100)
            ->get(['id', 'name', 'email']);
    }

    private function selectedCalendarIds(Request $request): array
    {
        $preference = $this->calendarPreference($request);

        $visibleIds = $this->visibleCalendarIds($request->user());

        if ($request->has('calendars_filter')) {
            $selectedIds = collect(Arr::wrap($request->input('category_ids', [])))
                ->filter(fn ($id): bool => is_numeric($id))
                ->map(fn ($id): int => (int) $id)
                ->intersect($visibleIds)
                ->values()
                ->all();

            $preference->update(['visible_calendar_categories' => $selectedIds]);

            return $selectedIds;
        }

        return collect($preference->visible_calendar_categories ?? $this->defaultVisibleCalendarIds($request->user()))
            ->intersect($visibleIds)
            ->values()
            ->all();
    }

    private function calendarPreference(Request $request): UserCalendarPreference
    {
        return UserCalendarPreference::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'default_view' => 'month',
                'timezone' => $request->user()->profile?->timezone ?? 'PST',
                'visible_calendar_categories' => $this->defaultVisibleCalendarIds($request->user()),
                'show_weekends' => true,
            ]
        );
    }

    private function visibleCategories(User $user): Collection
    {
        return CalendarCategory::query()
            ->visibleTo($user)
            ->orderByRaw('user_id is null desc')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function assignableCategories(User $user): Collection
    {
        return CalendarCategory::query()
            ->where('is_active', true)
            ->where(function (Builder $query) use ($user): void {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $user->id);
            })
            ->orderByRaw('user_id is null desc')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function visibleCalendarIds(User $user): array
    {
        return $this->visibleCategories($user)
            ->pluck('id')
            ->map(fn (int $id): int => $id)
            ->all();
    }

    private function defaultVisibleCalendarIds(User $user): array
    {
        return CalendarCategory::query()
            ->where('is_active', true)
            ->where(function (Builder $query) use ($user): void {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $user->id);
            })
            ->orderByRaw('user_id is null desc')
            ->orderBy('sort_order')
            ->pluck('id')
            ->map(fn (int $id): int => $id)
            ->all();
    }

    private function canManageCategory(User $user, CalendarCategory $category): bool
    {
        if ($category->isOwnedBy($user)) {
            return true;
        }

        return $category->isSystem() && $user->hasPermissionTo('manage organization calendar');
    }

    private function uniqueCategorySlug(int $userId, string $name): string
    {
        $base = 'u'.$userId.'-'.Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (CalendarCategory::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function safeReturnUrl(string $url): string
    {
        return str_starts_with($url, url('/')) || str_starts_with($url, '/')
            ? $url
            : route('calendar.index');
    }

    private function recurrenceData(array $validated): array
    {
        $frequency = $validated['recurrence_frequency'] ?? 'weekly';
        $interval = (int) ($validated['recurrence_interval'] ?? 1);
        $start = CarbonImmutable::parse($validated['starts_at']);
        $weekdays = $frequency === 'weekly'
            ? array_values(array_unique($validated['recurrence_weekdays'] ?? [$this->weekdayCode($start)]))
            : null;
        $endType = $validated['recurrence_end_type'] ?? 'never';
        $endsAfter = $endType === 'after' ? (int) ($validated['recurrence_ends_after_occurrences'] ?? 12) : null;
        $endsOn = $endType === 'on' ? $validated['recurrence_ends_on'] ?? null : null;

        $ruleParts = [
            'FREQ='.strtoupper($frequency),
            'INTERVAL='.$interval,
        ];

        if ($weekdays) {
            $ruleParts[] = 'BYDAY='.implode(',', $weekdays);
        }

        if ($endsAfter) {
            $ruleParts[] = 'COUNT='.$endsAfter;
        }

        if ($endsOn) {
            $ruleParts[] = 'UNTIL='.CarbonImmutable::parse($endsOn)->format('Ymd');
        }

        return [
            'frequency' => $frequency,
            'interval' => $interval,
            'weekdays' => $weekdays,
            'ends_after_occurrences' => $endsAfter,
            'ends_on' => $endsOn,
            'rule' => implode(';', $ruleParts),
        ];
    }

    private function weekdayCode(CarbonImmutable $date): string
    {
        return match ($date->dayOfWeekIso) {
            1 => 'MO',
            2 => 'TU',
            3 => 'WE',
            4 => 'TH',
            5 => 'FR',
            6 => 'SA',
            default => 'SU',
        };
    }

    private function dateRange(CarbonImmutable $currentDate, string $viewMode): array
    {
        return match ($viewMode) {
            'day' => [$currentDate->startOfDay(), $currentDate->endOfDay()],
            'agenda' => [$currentDate->startOfDay(), $currentDate->addDays(30)->endOfDay()],
            'week', 'work-week' => [$currentDate->startOfWeek(), $currentDate->endOfWeek()],
            default => [$currentDate->startOfMonth()->startOfWeek(), $currentDate->endOfMonth()->endOfWeek()],
        };
    }

    private function shiftedDate(CarbonImmutable $currentDate, string $viewMode, int $direction): string
    {
        return match ($viewMode) {
            'day' => $currentDate->addDays($direction)->toDateString(),
            'agenda' => $currentDate->addWeeks($direction)->toDateString(),
            'week', 'work-week' => $currentDate->addWeeks($direction)->toDateString(),
            default => $currentDate->addMonthsNoOverflow($direction)->toDateString(),
        };
    }

    private function miniCalendarDays(CarbonImmutable $date): Collection
    {
        return $this->daysBetween($date->startOfMonth()->startOfWeek(), $date->endOfMonth()->endOfWeek());
    }

    private function monthDays(CarbonImmutable $date): Collection
    {
        return $this->daysBetween($date->startOfMonth()->startOfWeek(), $date->endOfMonth()->endOfWeek());
    }

    private function weekDays(CarbonImmutable $rangeStart, bool $workWeek = false): Collection
    {
        $days = $this->daysBetween($rangeStart->startOfWeek(), $rangeStart->endOfWeek());

        return $workWeek ? $days->filter(fn (CarbonImmutable $day): bool => ! $day->isWeekend())->values() : $days;
    }

    private function daysBetween(CarbonImmutable $start, CarbonImmutable $end): Collection
    {
        $days = collect();

        for ($day = $start; $day->lte($end); $day = $day->addDay()) {
            $days->push($day);
        }

        return $days;
    }

    private function stats(Collection $events, Collection $upcomingEvents): array
    {
        return [
            'visible' => $events->count(),
            'upcoming' => $upcomingEvents->count(),
            'training' => $events->filter(fn (CalendarEvent $event): bool => str_contains((string) $event->type?->slug, 'training'))->count(),
            'prospects' => $events->filter(fn (CalendarEvent $event): bool => filled($event->related_prospect_id) || str_contains((string) $event->type?->slug, 'prospect'))->count(),
        ];
    }
}
