<x-app-layout>
    @php
        $titles = [
            'create' => 'Add Prospect',
            'pipeline' => 'Pipeline Board',
            'follow-ups' => 'Follow-Up Center',
            'appointments' => 'Appointment Calendar',
            'shared-with-me' => 'Shared With Me',
            'shared-by-me' => 'Shared By Me',
            'access-manager' => 'Access Manager',
            'import' => 'Prospect Import',
            'settings' => 'Prospect Settings',
        ];

        $descriptions = [
            'create' => 'Capture a personal prospect with source, stage, priority, interests, and contact details.',
            'pipeline' => 'Review your prospects by pipeline stage and activity status.',
            'follow-ups' => 'Track due dates, priority, status, and next actions for prospect follow-ups.',
            'appointments' => 'Manage scheduled calls, meetings, Zoom links, and helper assignments.',
            'shared-with-me' => 'Review prospect records explicitly shared with you.',
            'shared-by-me' => 'Review prospect records you have shared with collaborators.',
            'access-manager' => 'Audit active sharing permissions, expiration dates, and collaborator access.',
            'import' => 'Stage CSV imports, duplicate checks, skipped rows, and completed batches.',
            'settings' => 'Review lookup values used by prospect screens.',
        ];
    @endphp

    <section class="space-y-6">
        <div class="overflow-hidden rounded-lg border border-slate-400 bg-gradient-to-br from-white via-slate-50 to-[#FFF9EA] shadow-sm">
            <div class="flex flex-col gap-4 bg-[#0B1F3A] px-6 py-6 text-white md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Prospect Management</p>
                    <h1 class="mt-2 text-2xl font-semibold">{{ $titles[$screenKey] ?? 'Prospect Module' }}</h1>
                    <p class="mt-2 max-w-4xl text-sm leading-6 text-slate-200">{{ $descriptions[$screenKey] ?? 'Prospect module scaffold.' }}</p>
                </div>
                <a href="{{ route('team.prospects') }}" class="inline-flex items-center justify-center rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                    Back to Overview
                </a>
            </div>
        </div>

        @if ($screenKey === 'create')
            <div class="grid gap-6 xl:grid-cols-[.9fr_1.1fr]">
                <div class="rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Prospect Form Scaffold</h2>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        @foreach (['First Name', 'Last Name', 'Email', 'Phone', 'City', 'Occupation'] as $label)
                            <label class="block">
                                <span class="text-sm font-semibold text-slate-700">{{ $label }}</span>
                                <input type="text" disabled class="mt-1 block w-full rounded-lg border-slate-300 bg-white/80 text-sm shadow-sm" placeholder="Livewire field">
                            </label>
                        @endforeach
                    </div>
                    <div class="mt-4 grid gap-4 md:grid-cols-3">
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Source</span>
                            <select disabled class="mt-1 block w-full rounded-lg border-slate-300 bg-white/80 text-sm shadow-sm">
                                @foreach ($sources as $source)
                                    <option>{{ $source->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Stage</span>
                            <select disabled class="mt-1 block w-full rounded-lg border-slate-300 bg-white/80 text-sm shadow-sm">
                                @foreach ($pipelineStages as $stage)
                                    <option>{{ $stage->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="block">
                            <span class="text-sm font-semibold text-slate-700">Priority</span>
                            <select disabled class="mt-1 block w-full rounded-lg border-slate-300 bg-white/80 text-sm shadow-sm">
                                <option>Medium</option>
                                <option>High</option>
                                <option>Urgent</option>
                            </select>
                        </label>
                    </div>
                    <div class="mt-5 rounded-lg border border-dashed border-slate-400 bg-white/70 p-4 text-sm text-slate-600">
                        Save actions will be wired when the Prospect CRUD Livewire components are built.
                    </div>
                </div>

                @include('team.partials.prospect-prospect-table', ['rows' => $prospects, 'title' => 'Recent Prospects'])
            </div>
        @elseif ($screenKey === 'pipeline')
            @include('team.partials.prospect-prospect-table', ['rows' => $prospects, 'title' => 'Pipeline Prospect Table'])
        @elseif ($screenKey === 'follow-ups')
            @include('team.partials.prospect-followup-table', ['rows' => $followUps, 'title' => 'Follow-Up Table'])
        @elseif ($screenKey === 'appointments')
            @include('team.partials.prospect-appointment-table', ['rows' => $appointments, 'title' => 'Appointment Table'])
        @elseif ($screenKey === 'shared-with-me')
            @include('team.partials.prospect-shared-with-me-table', ['rows' => $sharedWithMe, 'title' => 'Shared With Me Table'])
        @elseif (in_array($screenKey, ['shared-by-me', 'access-manager'], true))
            @include('team.partials.prospect-share-table', ['rows' => $shares, 'title' => $screenKey === 'access-manager' ? 'Access Manager Table' : 'Shared By Me Table'])
        @elseif ($screenKey === 'import')
            @include('team.partials.prospect-import-table', ['rows' => $imports, 'title' => 'Import Batch Table'])
        @elseif ($screenKey === 'settings')
            <div class="grid gap-6 xl:grid-cols-2">
                @include('team.partials.prospect-lookup-table', ['rows' => $sources, 'title' => 'Sources'])
                @include('team.partials.prospect-lookup-table', ['rows' => $pipelineStages, 'title' => 'Pipeline Stages'])
                @include('team.partials.prospect-lookup-table', ['rows' => $types, 'title' => 'Types'])
                @include('team.partials.prospect-lookup-table', ['rows' => $interests, 'title' => 'Interests'])
            </div>
        @endif
    </section>
</x-app-layout>
