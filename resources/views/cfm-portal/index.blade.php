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
            editCountryId: @js(old('country_id', $portal['editForm']['country_id'] ?? '')),
            editProvinceId: @js(old('state_province_id', $portal['editForm']['state_province_id'] ?? '')),
            editProvinces: @js($portal['locationOptions']['provincesByCountryId']),
            showTraineeChecklistModal: false,
            checklistModalLoading: false,
            checklistModalError: null,
            checklistModalData: null,
            checklistModalView: 'accomplished',
            get editProvinceOptions() {
                return this.editProvinces[String(this.editCountryId)] || {};
            },
            onCountryChange() {
                const options = this.editProvinceOptions;
                if (this.editProvinceId && ! Object.prototype.hasOwnProperty.call(options, String(this.editProvinceId))) {
                    this.editProvinceId = '';
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
        class="cfm-portal-page space-y-6"
    >
        <div class="overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-[#F8F3E7] shadow-sm">
            @include('cfm-portal.partials.header')
        </div>

        @include('cfm-portal.partials.flash')
        @include('cfm-portal.partials.pending-assignments')

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Active Trainees</p>
                <p class="mt-2 text-2xl font-bold text-[#0B1F3A]">{{ $profile['activeApprentices'] }}/{{ $profile['maxApprentices'] }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $profile['pendingApprentices'] }} pending approval</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">FAP Completion Rate</p>
                <p class="mt-2 text-2xl font-bold text-emerald-700">{{ $profile['fapCompletionRate'] }}%</p>
                <p class="mt-1 text-xs text-slate-500">{{ $profile['completedApprentices'] }} graduates</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Recommendation Score</p>
                <p class="mt-2 text-2xl font-bold text-[#0B1F3A]">{{ $profile['recommendationScore'] }}/100</p>
                <p class="mt-1 text-xs font-medium" style="color: {{ $profile['recommendationColor'] }}">{{ $profile['recommendationBand'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Upcoming Sessions</p>
                <p class="mt-2 text-2xl font-bold text-[#0B1F3A]">{{ $profile['upcomingSessions'] }}</p>
                <p class="mt-1 text-xs text-slate-500">Next: {{ $profile['nextSlot'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            @include('cfm-portal.partials.profile')
            @include('cfm-portal.partials.achievements')
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @include('cfm-portal.partials.trainees')
            @include('cfm-portal.partials.training')
        </div>

        @include('cfm-portal.partials.rank-structure')

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @include('cfm-portal.partials.calendar')
            @include('cfm-portal.partials.activity')
        </div>

        @include('cfm-portal.partials.edit-profile-modal')
        @include('cfm-portal.partials.trainee-checklist-modal')
    </section>
</x-app-layout>
