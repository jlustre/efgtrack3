<x-app-layout>
    <div
        x-data="{
            profileCompletionOpen: @js(($forceProfileCompletionModal ?? false) || session('show_profile_completion_modal', false)),
            profileSaving: false,
            completionFields: @js($profileCompletion['fields'] ?? []),
            completionPercent: @js($profileCompletion['percent'] ?? 0),
            form: {
                name: @js($user->name),
                email: @js($user->email),
                phone: @js($user->profile?->phone ?? ''),
                best_contact_time: @js($user->profile?->best_contact_time ?? ''),
                efg_associate_id: @js($user->profile?->efg_associate_id ?? ''),
                efg_invite_link: @js($user->profile?->efg_invite_link ?? ''),
                city: @js($user->profile?->city ?? ''),
                country_id: @js((string) ($user->profile?->country_id ?? '')),
                state_province_id: @js((string) ($user->profile?->state_province_id ?? '')),
                timezone_id: @js((string) ($user->profile?->timezone_id ?? '')),
                bio: @js($user->profile?->bio ?? ''),
            },
            locationProvinces: @js($locationOptions['provincesByCountryId'] ?? []),
            get completionProvinceOptions() {
                return this.locationProvinces[String(this.form.country_id)] || {};
            },
            syncCompletionProvinceSelect() {
                const next = window.rebuildProvinceSelectOptions(
                    'completion_state_province_id',
                    this.completionProvinceOptions,
                    this.form.state_province_id,
                );
                if (next !== this.form.state_province_id) {
                    this.form.state_province_id = next;
                }
            },
            onCompletionCountryChange() {
                this.syncCompletionProvinceSelect();
            },
            get completedFieldCount() {
                return this.completionFields.filter((field) => field.filled).length;
            },
            dismissProfileCompletion() {
                this.profileCompletionOpen = false;
            },
        }"
        x-init="$nextTick(() => syncCompletionProvinceSelect())"
    >
    <div class="space-y-6">
        @include('profile.partials.member-header', [
            'user' => $user,
            'badge' => 'Member Dashboard',
            'showEfgDetails' => true,
        ])

        @include('dashboard.partials.stat-card-themes')

        <section
            x-data="dashboardStats(@js(['detailsUrlTemplate' => route('dashboard.stat-details', ['type' => '__TYPE__'])]))"
        >
            <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">My Team</h2>
            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-5">
                @foreach ($statCards['team'] ?? [] as $card)
                    @php
                        $theme = dashboardTrackerStatTheme($card['key']);
                        $bar = dashboardStatBarClasses($theme);
                    @endphp
                    <div class="space-y-3">
                        <x-tracker-stat-card
                            :label="$card['label']"
                            :value="$card['value']"
                            :theme="$theme"
                            subtitle="Team average completion"
                        />
                        <div class="flex items-center gap-2 px-1">
                            <div @class(['h-2 min-w-0 flex-1 rounded-full', $bar['track']])>
                                <div @class(['h-2 rounded-full', $bar['fill']]) style="width: {{ $card['bar'] }}%"></div>
                            </div>
                            <button
                                type="button"
                                class="inline-flex shrink-0 items-center gap-1 rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[0.62rem] font-semibold uppercase tracking-wide text-[#0B1F3A] transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]"
                                x-on:click="openModal(@js($card['key']), @js($card['label']))"
                                aria-label="View {{ $card['label'] }} details"
                            >
                                <svg class="h-3 w-3 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"></path>
                                </svg>
                                View
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            @include('dashboard.partials.stat-details-modal')
        </section>

        @if (! empty($statCards['personal'] ?? []))
            <section>
                <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">My Progress</h2>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                    @foreach ($statCards['personal'] as $card)
                        @php
                            $theme = dashboardTrackerStatTheme($card['key']);
                            $bar = dashboardStatBarClasses($theme);
                            $showBar = $card['show_bar'] ?? true;
                            $subtitle = match ($card['key']) {
                                'profile' => 'Complete your member profile',
                                'onboarding' => 'Onboarding checklist progress',
                                'credentials' => 'Licensing milestone progress',
                                'apprenticeship' => 'Field apprenticeship progress',
                                'training' => 'CFM training progress',
                                'prospects' => 'Active prospects in your CRM',
                                'hot_prospects' => 'High-priority leads',
                                'followups_due' => 'Due today or overdue',
                                'prospect_conversion' => 'Prospect to client rate',
                                'recruits' => 'Members in your downline',
                                'production' => 'Annual production total',
                                'fna' => 'Approved FNA submissions',
                                default => null,
                            };
                        @endphp
                        @if (! empty($card['url']))
                            <a href="{{ $card['url'] }}" class="block rounded-lg transition hover:scale-[1.01] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#C8A24A]">
                        @endif
                        <div class="space-y-2">
                            <x-tracker-stat-card
                                :label="$card['label']"
                                :value="$card['value']"
                                :theme="$theme"
                                :subtitle="$subtitle"
                            />
                            @if ($showBar)
                                <div class="px-1">
                                    <div @class(['h-1.5 rounded-full', $bar['track']])>
                                        <div @class(['h-1.5 rounded-full', $bar['fill']]) style="width: {{ $card['bar'] }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @if (! empty($card['url']))
                            </a>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        @include('dashboard.partials.journey-hub', ['overview' => $overview ?? []])

        @include('dashboard.partials.recent-notifications-panel')
    </div>

    @include('dashboard.partials.profile-completion-modal', [
        'user' => $user,
        'locationOptions' => $locationOptions,
    ])
    </div>
</x-app-layout>
