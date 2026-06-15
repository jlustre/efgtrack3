<x-app-layout>
    <div
        x-data="{
            profileCompletionOpen: @js($forceProfileCompletionModal ?? false),
            profileSaving: false,
            baseFields: @js($profileCompletion['fields']),
            form: {
                name: @js(old('name', $user->name)),
                email: @js(old('email', $user->email)),
                phone: @js(old('phone', $user->profile?->phone ?? '')),
                city: @js(old('city', $user->profile?->city ?? '')),
                country_id: @js(old('country_id', $user->profile?->country_id ?? '')),
                state_province_id: @js(old('state_province_id', $user->profile?->state_province_id ?? '')),
                timezone_id: @js(old('timezone_id', $user->profile?->timezone_id ?? '')),
                best_contact_time: @js(old('best_contact_time', $user->profile?->best_contact_time ?? '')),
                license_number: @js(old('license_number', $user->profile?->license_number ?? '')),
                efg_associate_id: @js(old('efg_associate_id', $user->profile?->efg_associate_id ?? '')),
                efg_invite_link: @js(old('efg_invite_link', $user->profile?->efg_invite_link ?? '')),
                bio: @js(old('bio', $user->profile?->bio ?? '')),
            },
            editProvinces: @js($locationOptions['provincesByCountryId']),
            get editProvinceOptions() {
                return this.editProvinces[String(this.form.country_id)] || {};
            },
            onCountryChange() {
                const options = this.editProvinceOptions;
                if (this.form.state_province_id && ! Object.prototype.hasOwnProperty.call(options, String(this.form.state_province_id))) {
                    this.form.state_province_id = '';
                }
            },
            fieldFilled(key) {
                const mappedKey = {
                    country: 'country_id',
                    country_id: 'country_id',
                    province: 'state_province_id',
                    state_province_id: 'state_province_id',
                    timezone: 'timezone_id',
                    timezone_id: 'timezone_id',
                }[key] ?? key;

                return String(this.form[mappedKey] ?? '').trim() !== '';
            },
            get completionFields() {
                return this.baseFields.map((field) => ({
                    ...field,
                    filled: this.fieldFilled(field.key),
                }));
            },
            get completionPercent() {
                if (! this.completionFields.length) {
                    return 0;
                }

<<<<<<< HEAD
    @include('dashboard.partials.stat-card-themes')

    <div class="space-y-6">
        <section
            x-data="dashboardStats(@js(['detailsUrlTemplate' => route('dashboard.stat-details', ['type' => '__TYPE__'])]))"
        >
            <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Team</h2>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5">
                @foreach ($statCards['team'] ?? [] as $card)
                    @php($theme = dashboardStatCardTheme($card['key'], 'team'))
                    <section @class([$theme['card']])>
                        <div class="flex items-center justify-between gap-2">
                            <h3 @class(['text-sm font-semibold', $theme['label']])>{{ $card['label'] }}</h3>
                            <span @class(['text-lg font-bold', $theme['value']])>{{ $card['value'] }}</span>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <div @class(['h-2 min-w-0 flex-1 rounded-full', $theme['bar_track']])>
                                <div @class(['h-2 rounded-full', $theme['bar_fill']]) style="width: {{ $card['bar'] }}%"></div>
                            </div>
                            <button
                                type="button"
                                @class(['inline-flex shrink-0 items-center gap-1 rounded-full border bg-white/60 px-2 py-0.5 text-[0.62rem] font-semibold uppercase tracking-wide transition backdrop-blur-sm', $theme['button']])
                                x-on:click="openModal(@js($card['key']), @js($card['label']))"
                                aria-label="View {{ $card['label'] }} details"
                            >
                                <svg @class(['h-3 w-3', $theme['icon']]) viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"></path>
                                </svg>
                                View
                            </button>
                        </div>
                    </section>
                @endforeach
            </div>

            @include('dashboard.partials.stat-details-modal')
        </section>

        @if (! empty($statCards['personal'] ?? []))
            <section>
                <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">My Progress</h2>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                    @foreach ($statCards['personal'] as $card)
                        @php($theme = dashboardStatCardTheme($card['key'], 'personal'))
                        @if (! empty($card['url']))
                            <a href="{{ $card['url'] }}" @class([$theme['card'], 'block transition hover:border-[#C8A24A]'])>
                        @else
                            <section @class([$theme['card']])>
                        @endif
                            <div class="flex items-center justify-between gap-2">
                                <h3 @class(['text-sm font-semibold', $theme['label']])>{{ $card['label'] }}</h3>
                                <span @class(['text-base font-bold', $theme['value']])>{{ $card['value'] }}</span>
                            </div>
                            @if ($card['show_bar'] ?? true)
                                <div @class(['mt-2 h-1.5 rounded-full', $theme['bar_track']])>
                                    <div @class(['h-1.5 rounded-full', $theme['bar_fill']]) style="width: {{ $card['bar'] }}%"></div>
                                </div>
                            @endif
                        @if (! empty($card['url']))
                            </a>
                        @else
                            </section>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    @include('dashboard.partials.journey-hub', ['overview' => $overview ?? []])

    <div class="mt-6">
        @include('dashboard.partials.recent-notifications-panel')
=======
                const filled = this.completionFields.filter((field) => field.filled).length;

                return Math.round((filled / this.completionFields.length) * 100);
            },
            get completedFieldCount() {
                return this.completionFields.filter((field) => field.filled).length;
            },
            dismissProfileCompletion() {
                this.profileCompletionOpen = false;
            },
            openProfileCompletion() {
                this.profileCompletionOpen = true;
            },
        }"
        x-init="
            if (@js($forceProfileCompletionModal ?? false) || @js($errors->any())) {
                profileCompletionOpen = true;
            }
        "
        class="space-y-6"
    >
        @if (session('profile_feedback') && (session('profile_feedback')['type'] ?? '') === 'success')
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
                {{ session('profile_feedback')['message'] }}
            </div>
        @endif

        @if (! $profileCompletion['is_complete'])
            <section class="rounded-lg border border-[#C8A24A]/40 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Profile setup</p>
                        <h2 class="mt-1 text-lg font-semibold text-[#0B1F3A]">Your profile is {{ $profileCompletion['percent'] }}% complete</h2>
                        <p class="mt-1 text-sm text-slate-600">Finish your details to unlock the full team experience.</p>
                    </div>
                    <button
                        type="button"
                        class="inline-flex items-center rounded-md bg-[#0B1F3A] px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-[#132F55]"
                        x-on:click="openProfileCompletion()"
                    >
                        Complete profile
                    </button>
                </div>
                <div class="mt-4 h-3 overflow-hidden rounded-full bg-slate-100">
                    <div class="h-3 rounded-full bg-[#C8A24A] transition-all duration-300" style="width: {{ $profileCompletion['percent'] }}%"></div>
                </div>
            </section>
        @endif

        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Private Team Portal</p>
                <h1 class="text-2xl font-semibold text-[#0B1F3A]">Dashboard</h1>
            </div>

            <div class="rounded-full border border-[#C8A24A]/40 bg-white px-4 py-2 text-sm font-semibold text-[#0B1F3A] shadow-sm">
                Current Rank: {{ auth()->user()?->rank?->code ?? 'New Recruit' }}
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            @foreach ($statCards as $card)
                <section
                    @class([
                        'rounded-lg border bg-white p-3 shadow-sm',
                        'border-slate-200' => ! ($card['interactive'] ?? false),
                        'cursor-pointer border-[#C8A24A]/40 transition hover:border-[#C8A24A] hover:shadow-md' => $card['interactive'] ?? false,
                    ])
                    @if ($card['interactive'] ?? false)
                        role="button"
                        tabindex="0"
                        x-on:click="openProfileCompletion()"
                        x-on:keydown.enter.prevent="openProfileCompletion()"
                        aria-label="Open profile completion form"
                    @endif
                >
                    <div class="flex items-center justify-between gap-1.5">
                        <h2 class="min-w-0 truncate text-xs font-semibold text-slate-600" title="{{ $card['label'] }}">{{ $card['label'] }}</h2>
                        <span class="shrink-0 text-base font-bold leading-none text-[#0B1F3A]">{{ $card['value'] }}</span>
                    </div>
                    <div class="mt-2 h-1.5 rounded-full bg-slate-100">
                        <div class="h-1.5 rounded-full bg-[#C8A24A] transition-all duration-300" style="width: {{ $card['bar'] }}%"></div>
                    </div>
                    @if ($card['interactive'] ?? false)
                        <p class="mt-1.5 truncate text-[0.65rem] font-semibold uppercase tracking-wide text-[#C8A24A]">Complete</p>
                    @endif
                </section>
            @endforeach
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-3">
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Next Rank Requirements</h2>
                    <span class="rounded-full bg-[#0B1F3A] px-3 py-1 text-xs font-semibold text-white">SFA Track</span>
                </div>

                <div class="space-y-3">
                    @foreach (['Complete onboarding checklist', 'Finish licensing milestones', 'Complete core training', 'Mentor review submitted'] as $item)
                        <div class="flex items-center justify-between rounded-md border border-slate-100 bg-slate-50 px-4 py-3">
                            <span class="text-sm font-medium">{{ $item }}</span>
                            <span class="text-xs font-semibold uppercase text-[#C8A24A]">In progress</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-[#0B1F3A]">Assigned CFM</h2>
                <div class="rounded-md bg-[#0B1F3A] p-4 text-white">
                    <div class="text-sm text-slate-300">Certified Field Mentor</div>
                    <div class="mt-1 text-xl font-semibold">Unassigned</div>
                    <div class="mt-4 h-1.5 rounded-full bg-white/20">
                        <div class="h-1.5 w-1/4 rounded-full bg-[#C8A24A]"></div>
                    </div>
                </div>
            </section>
        </div>

        <div class="mt-6 grid gap-6 xl:grid-cols-2">
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Team Communications</p>
                        <h2 class="mt-1 text-lg font-semibold text-[#0B1F3A]">Latest Announcements</h2>
                    </div>
                    <a href="{{ route('announcements.index') }}" class="rounded-full border border-[#C8A24A]/50 px-3 py-1 text-xs font-semibold text-[#0B1F3A] transition hover:bg-[#C8A24A]/10">
                        View All
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach ([
                        ['title' => 'Weekly leadership call reminder', 'meta' => 'Posted today', 'badge' => 'Priority'],
                        ['title' => 'New onboarding checklist updates', 'meta' => 'Posted 2 days ago', 'badge' => 'Training'],
                        ['title' => 'Recognition wall submissions now open', 'meta' => 'Posted this week', 'badge' => 'Culture'],
                    ] as $announcement)
                        <div class="rounded-md border border-slate-100 bg-slate-50 px-4 py-3">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="text-sm font-semibold text-[#0B1F3A]">{{ $announcement['title'] }}</h3>
                                <span class="shrink-0 rounded-full bg-[#0B1F3A] px-2 py-1 text-[0.68rem] font-semibold text-white">{{ $announcement['badge'] }}</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $announcement['meta'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Calendar</p>
                        <h2 class="mt-1 text-lg font-semibold text-[#0B1F3A]">Upcoming Events</h2>
                    </div>
                    <a href="{{ route('events.index') }}" class="rounded-full border border-[#C8A24A]/50 px-3 py-1 text-xs font-semibold text-[#0B1F3A] transition hover:bg-[#C8A24A]/10">
                        View Calendar
                    </a>
                </div>

                <div class="space-y-3">
                    @foreach ([
                        ['date' => 'Jun 04', 'title' => 'Licensing study session', 'time' => '6:00 PM PT'],
                        ['date' => 'Jun 08', 'title' => 'Field apprenticeship workshop', 'time' => '10:00 AM PT'],
                        ['date' => 'Jun 12', 'title' => 'Elite Financial Growth leadership huddle', 'time' => '7:00 PM PT'],
                    ] as $event)
                        <div class="flex gap-4 rounded-md border border-slate-100 bg-slate-50 px-4 py-3">
                            <div class="flex h-12 w-16 shrink-0 items-center justify-center rounded-md bg-[#0B1F3A] text-center text-xs font-bold uppercase leading-tight text-[#C8A24A]">
                                {{ $event['date'] }}
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-[#0B1F3A]">{{ $event['title'] }}</h3>
                                <p class="mt-1 text-xs text-slate-500">{{ $event['time'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        @include('dashboard.partials.profile-completion-modal')
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
    </div>
</x-app-layout>
