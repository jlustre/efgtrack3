<x-app-layout>
    @php
        $profile = $portal['profile'];
        $training = $portal['training'];
        $achievements = $portal['achievements'];
    @endphp

    <section
        x-data="{
            showEditProfileModal: @js($openEditProfileModal || $errors->any()),
            profileSaving: false,
            editCountry: @js(old('country', $portal['editForm']['country'] ?? '')),
            editProvince: @js(old('province', $portal['editForm']['province'] ?? '')),
            editProvinces: @js($portal['locationOptions']['provincesByCountry']),
            showTraineeChecklistModal: false,
            checklistModalLoading: false,
            checklistModalError: null,
            checklistModalData: null,
            checklistModalView: 'accomplished',
            get editProvinceOptions() {
                return this.editProvinces[this.editCountry] || {};
            },
            onCountryChange() {
                const options = this.editProvinceOptions;
                if (this.editProvince && ! Object.prototype.hasOwnProperty.call(options, this.editProvince)) {
                    this.editProvince = '';
                }
            },
            submitProfileForm() {
                this.profileSaving = true;
            },
            closeTraineeChecklistModal() {
                this.showTraineeChecklistModal = false;
                this.checklistModalLoading = false;
                this.checklistModalError = null;
                this.checklistModalData = null;
                this.checklistModalView = 'accomplished';
            },
            async openTraineeChecklistModal(url) {
                this.showTraineeChecklistModal = true;
                this.checklistModalLoading = true;
                this.checklistModalError = null;
                this.checklistModalData = null;
                this.checklistModalView = 'accomplished';

                try {
                    const response = await fetch(url, {
                        headers: {
                            Accept: 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    });

                    if (! response.ok) {
                        throw new Error('Failed to load checklist');
                    }

                    this.checklistModalData = await response.json();
                } catch (error) {
                    this.checklistModalError = 'Could not load mentoring checklist. Please try again.';
                } finally {
                    this.checklistModalLoading = false;
                }
            },
            accomplishedPhases() {
                if (! this.checklistModalData?.phases) {
                    return [];
                }

                return this.checklistModalData.phases
                    .map((phase) => {
                        const sections = phase.sections
                            .map((section) => ({
                                ...section,
                                items: section.items.filter((item) => item.is_completed),
                            }))
                            .filter((section) => section.items.length > 0);

                        return { ...phase, sections };
                    })
                    .filter((phase) => phase.sections.length > 0);
            },
            remainingPhases() {
                if (! this.checklistModalData?.phases) {
                    return [];
                }

                return this.checklistModalData.phases
                    .map((phase) => {
                        const sections = phase.sections
                            .map((section) => ({
                                ...section,
                                items: section.items.filter((item) => ! item.is_completed),
                            }))
                            .filter((section) => section.items.length > 0);

                        return { ...phase, sections };
                    })
                    .filter((phase) => phase.sections.length > 0);
            },
        }"
        x-init="
            @if (session('profile_feedback'))
                $nextTick(() => document.getElementById('cfm-portal-profile-feedback')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' }));
            @endif
        "
        class="cfm-management-page -mx-4 -mt-6 bg-black text-gray-200 font-sans antialiased sm:-mx-6 lg:-mx-8"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
            @include('cfm-portal.partials.flash')
            @include('cfm-portal.partials.header')

            @include('cfm-portal.partials.pending-assignments')

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-4">
                    <div class="text-amber-400 text-sm font-medium mb-1">Active Trainees</div>
                    <div class="text-2xl font-bold text-white">{{ $profile['activeApprentices'] }}/{{ $profile['maxApprentices'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $profile['pendingApprentices'] }} pending approval</div>
                </div>
                <div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-4">
                    <div class="text-green-400 text-sm font-medium mb-1">FAP Completion Rate</div>
                    <div class="text-2xl font-bold text-green-400">{{ $profile['fapCompletionRate'] }}%</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $profile['completedApprentices'] }} graduates</div>
                </div>
                <div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-4">
                    <div class="text-amber-400 text-sm font-medium mb-1">Recommendation Score</div>
                    <div class="text-2xl font-bold text-white">{{ $profile['recommendationScore'] }}/100</div>
                    <div class="text-xs mt-1" style="color: {{ $profile['recommendationColor'] }}">{{ $profile['recommendationBand'] }}</div>
                </div>
                <div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-4">
                    <div class="text-amber-400 text-sm font-medium mb-1">Upcoming Sessions</div>
                    <div class="text-2xl font-bold text-white">{{ $profile['upcomingSessions'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Next: {{ $profile['nextSlot'] }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                @include('cfm-portal.partials.profile')
                @include('cfm-portal.partials.achievements')
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                @include('cfm-portal.partials.trainees')
                @include('cfm-portal.partials.training')
            </div>

            @include('cfm-portal.partials.rank-structure')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
                @include('cfm-portal.partials.calendar')
                @include('cfm-portal.partials.activity')
            </div>
        </div>

        @include('cfm-portal.partials.edit-profile-modal')
        @include('cfm-portal.partials.trainee-checklist-modal')
    </section>
</x-app-layout>
