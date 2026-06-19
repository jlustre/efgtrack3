@php
    $isOwnProfile = $isOwnProfile ?? true;
    $hasNonEfgValidationErrors = $errors->any() && collect($errors->keys())->contains(
        fn (string $key): bool => ! in_array($key, ['efg_associate_id', 'efg_invite_link'], true)
            && ! str_starts_with($key, 'insurance_licenses')
    );
    $hasLicenseValidationErrors = $errors->has('insurance_licenses') || $errors->has('insurance_licenses.*');
    $tabs = [
        'profile' => 'Profile Details',
        'licenses' => 'Licenses',
        'checklists' => 'Checklists',
        'recruits' => 'Recruits',
        'annual-premium' => 'Annual Premium',
    ];
@endphp

<section
    x-data="{
        activeTab: @js(request('tab', 'profile')),
        profileSaving: false,
        licensesSaving: false,
        editCountryId: @js((string) old('country_id', $user->profile?->country_id ?? '')),
        editProvinceId: @js((string) old('state_province_id', $user->profile?->state_province_id ?? '')),
        editProvinces: @js($profileContext['locationOptions']['provincesByCountryId']),
        get editProvinceOptions() {
            return this.editProvinces[String(this.editCountryId)] || {};
        },
        syncProvinceSelect(selectId = 'state_province_id') {
            const next = window.rebuildProvinceSelectOptions(selectId, this.editProvinceOptions, this.editProvinceId);
            if (next !== this.editProvinceId) {
                this.editProvinceId = next;
            }
        },
        onCountryChange() {
            this.syncProvinceSelect('state_province_id');
        },
        submitProfileForm() {
            this.profileSaving = true;
        },
    }"
    x-init="
        $nextTick(() => syncProvinceSelect('state_province_id'));
        @if (session('licenses_feedback') || $hasLicenseValidationErrors)
            activeTab = 'licenses';
            $nextTick(() => document.getElementById('member-licenses-feedback')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' }));
        @elseif (session('profile_feedback') || $hasNonEfgValidationErrors)
            activeTab = 'profile';
            $nextTick(() => document.getElementById('member-profile-feedback')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' }));
        @endif
    "
    class="rounded-lg border border-slate-200 bg-white shadow-sm"
>
    <div class="border-b border-slate-200 bg-slate-50/80 px-4 pt-4">
        <nav class="-mb-px flex flex-wrap gap-2" aria-label="Member profile sections">
            @foreach ($tabs as $key => $label)
                <button
                    type="button"
                    @click="activeTab = @js($key)"
                    :class="activeTab === @js($key)
                        ? 'border-[#C8A24A] bg-white text-[#0B1F3A] shadow-sm'
                        : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-[#0B1F3A]'"
                    class="rounded-t-lg border px-4 py-2.5 text-sm font-semibold transition"
                >
                    {{ $label }}
                    @if ($key === 'annual-premium')
                        <span class="ml-1 rounded-full bg-[#C8A24A]/20 px-2 py-0.5 text-xs text-[#8A6A1F]">${{ number_format($memberTabs['annualPremiumTotal']) }}</span>
                    @elseif ($key === 'licenses' && ($user->profile?->insurance_licenses ?? []) !== [])
                        <span class="ml-1 rounded-full bg-[#C8A24A]/20 px-2 py-0.5 text-xs text-[#8A6A1F]">{{ count($user->profile->insurance_licenses) }}</span>
                    @endif
                </button>
            @endforeach
        </nav>
    </div>

    <div class="p-6">
        <div x-show="activeTab === 'profile'">
            @include('profile.partials.profile-feedback')
            @include('profile.partials.update-profile-information-form')

            @if ($isOwnProfile)
            <div class="mt-8 grid gap-8 border-t border-slate-200 pt-8 lg:grid-cols-2">
                @include('profile.partials.update-profile-photo-form')
                @include('profile.partials.update-password-form')
            </div>
            @endif
        </div>

        <div x-show="activeTab === 'licenses'" x-cloak>
            @include('profile.partials.licenses-tab', [
                'user' => $user,
                'isOwnProfile' => $isOwnProfile,
                'profileContext' => $profileContext,
            ])
        </div>

        <div x-show="activeTab === 'checklists'" x-cloak>
            @include('profile.partials.checklists-tab', [
                'user' => $user,
                'memberTabs' => $memberTabs,
                'isOwnProfile' => $isOwnProfile,
            ])
        </div>

        <div x-show="activeTab === 'recruits'" x-cloak>
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Recruits</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Your downline by sponsorship level. Level 1 members are your direct recruits.
                    </p>
                    <p class="mt-2 text-sm font-semibold text-[#0B1F3A]">
                        Total recruits: {{ number_format($memberTabs['recruitsTotal']) }}
                        @if ($memberTabs['recruitsDirectTotal'] > 0)
                            <span class="font-normal text-slate-600">({{ number_format($memberTabs['recruitsDirectTotal']) }} at Level 1)</span>
                        @endif
                    </p>
                </div>
                <a href="{{ route('team.tree') }}" class="text-sm font-semibold text-[#8A6A1F] hover:underline">View Genealogy →</a>
            </div>
            @include('profile.partials.tab-table-filterable', [
                'tableKey' => 'recruits',
                'columns' => [
                    ['key' => 'level_label', 'label' => 'Level'],
                    ['key' => 'member', 'label' => 'Member', 'type' => 'member'],
                    ['key' => 'role', 'label' => 'Role', 'type' => 'role'],
                    ['key' => 'rank', 'label' => 'Rank'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'sponsor', 'label' => 'Sponsor'],
                    ['key' => 'cfm', 'label' => 'CFM'],
                    ['key' => 'location', 'label' => 'Location', 'type' => 'location'],
                    ['key' => 'joined_at', 'label' => 'Joined'],
                    ['key' => 'onboarding', 'label' => 'Onboarding'],
                    ['key' => 'fap', 'label' => 'FAP'],
                ],
                'rows' => $memberTabs['recruits'],
                'searchKeys' => ['level_label', 'name', 'email', 'phone', 'role', 'rank', 'status', 'sponsor', 'cfm', 'province', 'country', 'country_flag', 'joined_at', 'onboarding', 'fap'],
                'searchPlaceholder' => 'Search by name, email, phone, role, rank, sponsor…',
                'filterFields' => [
                    [
                        'key' => 'level_label',
                        'label' => 'Level',
                        'allLabel' => 'All levels',
                        'dynamic' => true,
                    ],
                    [
                        'key' => 'status',
                        'label' => 'Status',
                        'allLabel' => 'All statuses',
                        'options' => [
                            ['value' => '', 'label' => 'All statuses'],
                            ['value' => 'Active', 'label' => 'Active'],
                            ['value' => 'Inactive', 'label' => 'Inactive'],
                        ],
                    ],
                    ['key' => 'rank', 'label' => 'Rank', 'allLabel' => 'All ranks', 'dynamic' => true],
                    ['key' => 'role', 'label' => 'Role', 'allLabel' => 'All roles', 'dynamic' => true],
                    ['key' => 'province', 'label' => 'Province/State', 'allLabel' => 'All provinces', 'dynamic' => true],
                    ['key' => 'country', 'label' => 'Country', 'allLabel' => 'All countries', 'dynamic' => true],
                    ['key' => 'cfm', 'label' => 'CFM', 'allLabel' => 'All CFMs', 'dynamic' => true],
                ],
                'empty' => 'You have not recruited any team members yet.',
                'emptyFiltered' => 'No recruits match your search or filters.',
            ])
        </div>

        <div x-show="activeTab === 'annual-premium'" x-cloak>
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Annual Premium</h2>
                    <p class="mt-1 text-sm text-slate-600">Current-year production attributed to your milestones and team growth.</p>
                </div>
                <div class="rounded-lg border border-[#C8A24A]/30 bg-[#FFF9EA] px-4 py-2 text-sm font-semibold text-[#0B1F3A]">
                    YTD Total: ${{ number_format($memberTabs['annualPremiumTotal']) }}
                </div>
            </div>
            @include('profile.partials.tab-table-filterable', [
                'tableKey' => 'annual-premium',
                'columns' => [
                    ['key' => 'period', 'label' => 'Period'],
                    ['key' => 'source', 'label' => 'Source'],
                    ['key' => 'description', 'label' => 'Description'],
                    ['key' => 'annual_premium', 'label' => 'Annual Premium'],
                    ['key' => 'status', 'label' => 'Status'],
                    ['key' => 'posted_at', 'label' => 'Posted'],
                ],
                'rows' => $memberTabs['annualPremium'],
                'searchKeys' => ['period', 'source', 'description', 'annual_premium', 'status', 'posted_at'],
                'searchPlaceholder' => 'Search by source, description, period…',
                'filterFields' => [
                    ['key' => 'period', 'label' => 'Period', 'allLabel' => 'All periods', 'dynamic' => true],
                    [
                        'key' => 'source',
                        'label' => 'Source',
                        'allLabel' => 'All sources',
                        'options' => [
                            ['value' => '', 'label' => 'All sources'],
                            ['value' => 'Onboarding', 'label' => 'Onboarding'],
                            ['value' => 'Field Apprenticeship', 'label' => 'Field Apprenticeship'],
                            ['value' => 'CFM Training', 'label' => 'CFM Training'],
                            ['value' => 'Direct Recruit', 'label' => 'Direct Recruit'],
                        ],
                    ],
                    [
                        'key' => 'status',
                        'label' => 'Status',
                        'allLabel' => 'All statuses',
                        'options' => [
                            ['value' => '', 'label' => 'All statuses'],
                            ['value' => 'Posted', 'label' => 'Posted'],
                        ],
                    ],
                ],
                'sumKey' => 'premium_amount',
                'sumLabel' => 'Filtered total',
                'empty' => 'No annual premium production recorded yet.',
                'emptyFiltered' => 'No premium entries match your search or filters.',
            ])
        </div>
    </div>
</section>
