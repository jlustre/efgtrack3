<x-app-layout>
    @php
        $priorityClasses = [
            'urgent' => 'bg-red-100 text-red-700 border-red-300',
            'high' => 'bg-amber-100 text-amber-700 border-amber-300',
            'medium' => 'bg-blue-100 text-blue-700 border-blue-300',
            'low' => 'bg-slate-100 text-slate-700 border-slate-300',
        ];
    @endphp

    <section
        class="space-y-6"
        x-data="prospectActivitiesModal()"
        data-activity-types='@json(config('prospects.activity_types'))'
    >
        @if (session('prospect_quick_log_status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('prospect_quick_log_status') }}
            </div>
        @endif

        @include('team.prospects.partials.dashboard-hero')
        @include('team.prospects.partials.quick-nav')
        <livewire:prospects.prospect-activity-log-summary />
        @include('team.prospects.partials.pipeline-followups', ['priorityClasses' => $priorityClasses])
        @include('team.prospects.partials.prospect-directory')
        @include('team.prospects.partials.insights-panels')
        @include('team.prospects.partials.module-shortcuts')

        @include('team.partials.prospect-activities-modal')
    </section>

    @include('prospects.partials.mobile-quick-actions')

    <livewire:prospects.log-activity-modal />
    <livewire:prospects.log-communication-modal />
    <livewire:prospects.prospect-quick-log-modal />
</x-app-layout>
