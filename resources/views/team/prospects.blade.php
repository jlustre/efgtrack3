<x-app-layout>
    @php
        $pipelineMax = max(1, (int) $pipelineSummary->max('prospect_count'));
        $sourceMax = max(1, (int) $sourcePerformance->max('prospect_count'));
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
        data-activity-types='@json(\App\Models\ProspectActivity::TYPES)'
    >
        <div class="overflow-hidden rounded-lg border border-slate-400 bg-gradient-to-br from-white via-slate-50 to-[#FFF9EA] shadow-sm">
            <div class="flex flex-col gap-4 bg-[#0B1F3A] px-6 py-6 text-white lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Prospect Management</p>
                    <h1 class="mt-2 text-2xl font-semibold">Private CRM workspace for personal prospects</h1>
                    <p class="mt-2 max-w-4xl text-sm leading-6 text-slate-200">
                        Manage prospects, follow-ups, communication history, appointments, conversion pipeline, and controlled sharing with mentors or leaders.
                    </p>
                </div>
                <a href="{{ route('team.prospects.create') }}" class="inline-flex items-center justify-center rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] shadow-sm transition hover:bg-[#D8B85F]">
                    Add Prospect
                </a>
            </div>

            <div class="grid gap-4 p-6 md:grid-cols-2 xl:grid-cols-7">
                @foreach ([
                    ['label' => 'My Prospects', 'value' => $stats['total'], 'class' => 'border-[#C8A24A]/60 bg-[#FFF9EA]'],
                    ['label' => 'Hot Prospects', 'value' => $stats['hot'], 'class' => 'border-red-300 bg-red-50'],
                    ['label' => 'Follow-Ups Due', 'value' => $stats['followups_due'], 'class' => 'border-amber-300 bg-amber-50'],
                    ['label' => 'Appointments', 'value' => $stats['appointments'], 'class' => 'border-sky-300 bg-sky-50'],
                    ['label' => 'Shared With Me', 'value' => $stats['shared_with_me'], 'class' => 'border-purple-300 bg-purple-50'],
                    ['label' => 'Shared By Me', 'value' => $stats['shared_by_me'], 'class' => 'border-indigo-300 bg-indigo-50'],
                    ['label' => 'Conversion Rate', 'value' => $stats['conversion_rate'].'%', 'class' => 'border-emerald-300 bg-emerald-50'],
                ] as $card)
                    <div class="rounded-lg border p-5 shadow-sm {{ $card['class'] }}">
                        <p class="text-sm font-semibold text-slate-600">{{ $card['label'] }}</p>
                        <div class="mt-3 text-3xl font-semibold text-[#0B1F3A]">{{ $card['value'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.15fr_.85fr]">
            <div x-data="{ expanded: false }" class="relative rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-[#0B1F3A]">Pipeline Summary</h2>
                        <p class="mt-1 text-sm text-slate-600">Stage counts for your active prospects.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="absolute right-[10px] top-[10px] rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2.5 py-1 text-xs font-bold text-[#0B1F3A]">{{ $pipelineSummary->sum('prospect_count') }}</span>
                        <a href="{{ route('team.prospects.screen', 'pipeline') }}" class="text-sm font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">Open Board</a>
                        <button type="button" x-on:click="expanded = ! expanded" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-300 bg-white text-[#0B1F3A] shadow-sm transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">
                            <span class="sr-only">Toggle Pipeline Summary</span>
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': expanded }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div x-show="expanded" x-transition>
                <div class="mt-5 overflow-x-auto rounded-lg border border-slate-300 bg-white/85">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Stage</th>
                                <th class="px-4 py-3">Prospects</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($pipelineSummary as $stage)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-[#0B1F3A]">{{ $stage->name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $stage->prospect_count }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $stage->prospect_count > 0 ? 'Active' : 'Empty' }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            <div x-data="{ expanded: false }" class="relative rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-amber-50 p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-[#0B1F3A]">Follow-Up Center</h2>
                        <p class="mt-1 text-sm text-slate-600">Tasks due today or already overdue.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="absolute right-[10px] top-[10px] rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2.5 py-1 text-xs font-bold text-[#0B1F3A]">{{ $followUpsTable->count() }}</span>
                        <a href="{{ route('team.prospects.screen', 'follow-ups') }}" class="text-sm font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">View All</a>
                        <button type="button" x-on:click="expanded = ! expanded" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-300 bg-white text-[#0B1F3A] shadow-sm transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">
                            <span class="sr-only">Toggle Follow-Up Center</span>
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': expanded }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div x-show="expanded" x-transition>
                <div class="hidden">
                    @forelse ($followUpsDueToday as $followUp)
                        <div class="rounded-lg border border-slate-300 bg-white/85 p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#0B1F3A]">{{ trim($followUp->first_name.' '.$followUp->last_name) }}</p>
                                    <p class="mt-1 text-sm text-slate-600">{{ $followUp->followup_type ?? 'Follow up' }} · {{ \Illuminate\Support\Carbon::parse($followUp->due_at)->format('M j, g:i A') }}</p>
                                </div>
                                <span class="rounded-full border px-2.5 py-1 text-xs font-bold uppercase {{ $priorityClasses[$followUp->priority] ?? $priorityClasses['medium'] }}">
                                    {{ $followUp->priority }}
                                </span>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>

                <div class="mt-5 overflow-x-auto rounded-lg border border-slate-300 bg-white/85">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Prospect</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Due</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($followUpsTable as $followUp)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-[#0B1F3A]">{{ trim($followUp->first_name.' '.$followUp->last_name) }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $followUp->followup_type ?? 'Follow up' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ \Illuminate\Support\Carbon::parse($followUp->due_at)->format('M j, g:i A') }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ str($followUp->status)->title() }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>

        <div class="relative rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-6 shadow-sm">
            <span class="absolute right-[10px] top-[10px] rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2.5 py-1 text-xs font-bold text-[#0B1F3A]">{{ $allProspects->total() }}</span>

            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">All Prospects</h2>
                    <p class="mt-1 text-sm text-slate-600">Complete prospect list across active, archived, converted, and inactive statuses.</p>
                </div>
                <a href="{{ route('team.prospects.create') }}" class="inline-flex items-center justify-center rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-3 py-2 text-sm font-semibold text-[#0B1F3A] shadow-sm transition hover:bg-[#D8B85F]">
                    Add Prospect
                </a>
            </div>

            <form method="GET" action="{{ route('team.prospects') }}" class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-6">
                <label class="block xl:col-span-2">
                    <span class="sr-only">Search prospects</span>
                    <input
                        type="search"
                        name="prospect_search"
                        value="{{ request('prospect_search') }}"
                        placeholder="Search name, email, phone, city..."
                        class="block h-10 w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
                    >
                </label>

                <label class="block">
                    <span class="sr-only">Status</span>
                    <select name="prospect_status" class="block h-10 w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                        <option value="">All Statuses</option>
                        @foreach ($prospectStatuses as $status)
                            <option value="{{ $status }}" @selected(request('prospect_status') === $status)>{{ str($status)->title() }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="sr-only">Pipeline stage</span>
                    <select name="prospect_stage" class="block h-10 w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                        <option value="">All Stages</option>
                        @foreach ($pipelineStages as $stage)
                            <option value="{{ $stage->id }}" @selected((string) request('prospect_stage') === (string) $stage->id)>{{ $stage->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="sr-only">Source</span>
                    <select name="prospect_source" class="block h-10 w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                        <option value="">All Sources</option>
                        @foreach ($prospectSources as $source)
                            <option value="{{ $source->id }}" @selected((string) request('prospect_source') === (string) $source->id)>{{ $source->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="sr-only">Interest</span>
                    <select name="prospect_interest" class="block h-10 w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                        <option value="">All Interest</option>
                        @foreach (['cold', 'warm', 'hot'] as $interest)
                            <option value="{{ $interest }}" @selected(request('prospect_interest') === $interest)>{{ str($interest)->title() }}</option>
                        @endforeach
                    </select>
                </label>

                <div class="flex gap-2 xl:col-span-6">
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#12345B]">
                        Apply
                    </button>
                    <a href="{{ route('team.prospects') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">
                        Reset
                    </a>
                </div>
            </form>

            <div class="mt-5 overflow-x-auto rounded-lg border border-slate-300 bg-white/85">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Prospect</th>
                            <th class="px-4 py-3">Contact</th>
                            <th class="px-4 py-3">Stage</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Interest</th>
                            <th class="px-4 py-3">Priority</th>
                            <th class="px-4 py-3">Next Follow-Up</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($allProspects as $prospect)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-[#0B1F3A]">{{ $prospect->first_name }} {{ $prospect->last_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $prospect->city ?? 'City not set' }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    <div>{{ $prospect->email ?? 'Email not set' }}</div>
                                    <div class="text-xs text-slate-500">{{ $prospect->phone ?? 'Phone not set' }}</div>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $prospect->stage?->name ?? 'No Stage' }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ str($prospect->status)->title() }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ str($prospect->interest_level)->title() }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ str($prospect->priority)->title() }}</td>
                                <td class="px-4 py-3 text-slate-600">{{ $prospect->next_follow_up_at?->format('M j, g:i A') ?? 'Not scheduled' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <button
                                            type="button"
                                            title="Activities"
                                            class="inline-flex shrink-0 p-0.5 text-slate-500 transition hover:text-[#C8A24A]"
                                            x-on:click="openFor({ id: @js($prospect->id), name: @js(trim($prospect->first_name.' '.$prospect->last_name)) })"
                                        >
                                            <span class="sr-only">Activities</span>
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M8 6h13"></path><path d="M8 12h13"></path><path d="M8 18h13"></path><path d="M3 6h.01"></path><path d="M3 12h.01"></path><path d="M3 18h.01"></path></svg>
                                        </button>
                                        <a href="{{ route('team.prospects.records.show', $prospect) }}" title="View prospect" class="inline-flex shrink-0 p-0.5 text-slate-500 transition hover:text-[#C8A24A]">
                                            <span class="sr-only">View prospect</span>
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        </a>
                                        <a href="{{ route('team.prospects.records.edit', $prospect) }}" title="Edit prospect" class="inline-flex shrink-0 p-0.5 text-slate-500 transition hover:text-[#C8A24A]">
                                            <span class="sr-only">Edit prospect</span>
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 20h9"></path><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path></svg>
                                        </a>
                                        <form method="POST" action="{{ route('team.prospects.records.archive', $prospect) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="Archive prospect" class="inline-flex shrink-0 p-0.5 text-slate-500 transition hover:text-[#C8A24A]">
                                                <span class="sr-only">Archive prospect</span>
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 8v13H3V8"></path><path d="M1 3h22v5H1z"></path><path d="M10 12h4"></path></svg>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('team.prospects.records.destroy', $prospect) }}" class="inline" onsubmit="return confirm('Delete this prospect?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete prospect" class="inline-flex shrink-0 p-0.5 text-slate-500 transition hover:text-red-600">
                                                <span class="sr-only">Delete prospect</span>
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6l-1 14H6L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $allProspects->links() }}
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div x-data="{ expanded: false }" class="relative rounded-lg border border-slate-400 bg-gradient-to-br from-white via-red-50 to-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Hot Prospects</h2>
                    <div class="flex items-center gap-3">
                        <span class="absolute right-[10px] top-[10px] rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2.5 py-1 text-xs font-bold text-[#0B1F3A]">{{ $hotProspects->count() }}</span>
                        <a href="{{ route('team.prospects.screen', 'pipeline') }}" class="text-sm font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">Pipeline</a>
                        <button type="button" x-on:click="expanded = ! expanded" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-300 bg-white text-[#0B1F3A] shadow-sm transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">
                            <span class="sr-only">Toggle Hot Prospects</span>
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': expanded }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div x-show="expanded" x-transition>
                <div class="hidden">
                    @forelse ($hotProspects as $prospect)
                        <div class="rounded-lg border border-slate-300 bg-white/85 p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#0B1F3A]">{{ $prospect->first_name }} {{ $prospect->last_name }}</p>
                                    <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $prospect->stage?->name ?? 'No Stage' }}</p>
                                    <p class="mt-2 text-sm text-slate-600">{{ $prospect->source?->name ?? 'Source not set' }}</p>
                                </div>
                                <span class="rounded-full border px-2.5 py-1 text-xs font-bold uppercase {{ $priorityClasses[$prospect->priority] ?? $priorityClasses['medium'] }}">
                                    {{ $prospect->priority }}
                                </span>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>

                <div class="mt-5 overflow-x-auto rounded-lg border border-slate-300 bg-white/85">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Prospect</th>
                                <th class="px-4 py-3">Stage</th>
                                <th class="px-4 py-3">Priority</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($hotProspects as $prospect)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-[#0B1F3A]">{{ $prospect->first_name }} {{ $prospect->last_name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $prospect->stage?->name ?? 'No Stage' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ str($prospect->priority)->title() }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            <div x-data="{ expanded: false }" class="relative rounded-lg border border-slate-400 bg-gradient-to-br from-white via-sky-50 to-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Appointment Calendar</h2>
                    <div class="flex items-center gap-3">
                        <span class="absolute right-[10px] top-[10px] rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2.5 py-1 text-xs font-bold text-[#0B1F3A]">{{ $appointmentsTable->count() }}</span>
                        <a href="{{ route('team.prospects.screen', 'appointments') }}" class="text-sm font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">Calendar</a>
                        <button type="button" x-on:click="expanded = ! expanded" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-300 bg-white text-[#0B1F3A] shadow-sm transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">
                            <span class="sr-only">Toggle Appointment Calendar</span>
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': expanded }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div x-show="expanded" x-transition>
                <div class="hidden">
                    @forelse ($upcomingAppointments as $appointment)
                        <div class="rounded-lg border border-slate-300 bg-white/85 p-4 shadow-sm">
                            <p class="font-semibold text-[#0B1F3A]">{{ trim($appointment->first_name.' '.$appointment->last_name) }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ \Illuminate\Support\Carbon::parse($appointment->scheduled_at)->format('M j, g:i A') }}</p>
                            <p class="mt-2 text-sm text-slate-600">{{ $appointment->appointment_type ?? 'Appointment' }} · {{ $appointment->purpose ?? 'Prospect meeting' }}</p>
                            @if ($appointment->helper_name)
                                <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-[#8A6A1F]">With {{ $appointment->helper_name }}</p>
                            @endif
                        </div>
                    @empty
                    @endforelse
                </div>

                <div class="mt-5 overflow-x-auto rounded-lg border border-slate-300 bg-white/85">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Prospect</th>
                                <th class="px-4 py-3">When</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($appointmentsTable as $appointment)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-[#0B1F3A]">{{ trim($appointment->first_name.' '.$appointment->last_name) }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ \Illuminate\Support\Carbon::parse($appointment->scheduled_at)->format('M j, g:i A') }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ str($appointment->status)->title() }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            <div x-data="{ expanded: false }" class="relative rounded-lg border border-slate-400 bg-gradient-to-br from-white via-emerald-50 to-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Reports Snapshot</h2>
                    <div class="flex items-center gap-3">
                        <span class="absolute right-[10px] top-[10px] rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2.5 py-1 text-xs font-bold text-[#0B1F3A]">{{ $stats['converted'] }}</span>
                        <button type="button" x-on:click="expanded = ! expanded" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-300 bg-white text-[#0B1F3A] shadow-sm transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">
                            <span class="sr-only">Toggle Reports Snapshot</span>
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': expanded }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div x-show="expanded" x-transition>
                <div class="hidden">
                    <div class="rounded-lg border border-slate-300 bg-white/85 p-4">
                        <p class="text-sm font-semibold text-slate-600">Converted Prospects</p>
                        <p class="mt-2 text-2xl font-semibold text-[#0B1F3A]">{{ $stats['converted'] }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-300 bg-white/85 p-4">
                        <p class="text-sm font-semibold text-slate-600">Best Sources</p>
                        <div class="mt-3 space-y-3">
                            @foreach ($sourcePerformance as $source)
                                <div>
                                    <div class="flex justify-between text-sm">
                                        <span>{{ $source->name }}</span>
                                        <span class="font-semibold">{{ $source->prospect_count }}</span>
                                    </div>
                                    <div class="mt-1 h-2 overflow-hidden rounded-full border border-slate-300 bg-white">
                                        <div class="h-full bg-emerald-500" style="width: {{ ((int) $source->prospect_count / $sourceMax) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-5 overflow-x-auto rounded-lg border border-slate-300 bg-white/85">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Source</th>
                                <th class="px-4 py-3">Prospects</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($sourcePerformance as $source)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-[#0B1F3A]">{{ $source->name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $source->prospect_count }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div x-data="{ expanded: false }" class="relative rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Communication Timeline</h2>
                    <div class="flex items-center gap-3">
                        <span class="absolute right-[10px] top-[10px] rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2.5 py-1 text-xs font-bold text-[#0B1F3A]">{{ $recentCommunications->count() }}</span>
                        <button type="button" x-on:click="expanded = ! expanded" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-300 bg-white text-[#0B1F3A] shadow-sm transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">
                            <span class="sr-only">Toggle Communication Timeline</span>
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': expanded }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div x-show="expanded" x-transition>
                <div class="hidden">
                    @forelse ($recentCommunications as $communication)
                        <div class="rounded-lg border border-slate-300 bg-white/85 p-4 shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#0B1F3A]">{{ trim($communication->first_name.' '.$communication->last_name) }}</p>
                                    <p class="mt-1 text-sm text-slate-600">{{ $communication->communication_type ?? 'Contact' }} · {{ $communication->outcome ?? 'Logged' }}</p>
                                    @if ($communication->next_action)
                                        <p class="mt-2 text-sm text-slate-600">{{ $communication->next_action }}</p>
                                    @endif
                                </div>
                                <span class="whitespace-nowrap text-xs font-semibold uppercase tracking-wide text-slate-500">{{ \Illuminate\Support\Carbon::parse($communication->contacted_at)->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>

                <div class="mt-5 overflow-x-auto rounded-lg border border-slate-300 bg-white/85">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Prospect</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Outcome</th>
                                <th class="px-4 py-3">Contacted</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($recentCommunications as $communication)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-[#0B1F3A]">{{ trim($communication->first_name.' '.$communication->last_name) }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $communication->communication_type ?? 'Contact' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $communication->outcome ?? 'Logged' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ \Illuminate\Support\Carbon::parse($communication->contacted_at)->format('M j') }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            <div x-data="{ expanded: false }" class="relative rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Recently Contacted</h2>
                    <div class="flex items-center gap-3">
                        <span class="absolute right-[10px] top-[10px] rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2.5 py-1 text-xs font-bold text-[#0B1F3A]">{{ $recentlyContactedProspects->count() }}</span>
                        <button type="button" x-on:click="expanded = ! expanded" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-300 bg-white text-[#0B1F3A] shadow-sm transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">
                            <span class="sr-only">Toggle Recently Contacted</span>
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': expanded }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div x-show="expanded" x-transition>
                <div class="hidden">
                    @forelse ($recentlyContactedProspects as $prospect)
                        <div class="rounded-lg border border-slate-300 bg-white/85 p-4 shadow-sm">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-[#0B1F3A]">{{ $prospect->first_name }} {{ $prospect->last_name }}</p>
                                    <p class="mt-1 text-sm text-slate-600">Last contact: {{ $prospect->last_contacted_at?->format('M j, g:i A') }}</p>
                                </div>
                                <span class="rounded-full border border-slate-300 bg-white px-2.5 py-1 text-xs font-bold uppercase text-slate-700">{{ $prospect->interest_level }}</span>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>

                <div class="mt-5 overflow-x-auto rounded-lg border border-slate-300 bg-white/85">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Prospect</th>
                                <th class="px-4 py-3">Last Contact</th>
                                <th class="px-4 py-3">Next Follow-Up</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($recentlyContactedProspects as $prospect)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-[#0B1F3A]">{{ $prospect->first_name }} {{ $prospect->last_name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $prospect->last_contacted_at?->format('M j, g:i A') ?? 'Not contacted' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $prospect->next_follow_up_at?->format('M j, g:i A') ?? 'Not scheduled' }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div x-data="{ expanded: false }" class="relative rounded-lg border border-slate-400 bg-gradient-to-br from-white via-purple-50 to-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Shared With Me</h2>
                    <div class="flex items-center gap-3">
                        <span class="absolute right-[10px] top-[10px] rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2.5 py-1 text-xs font-bold text-[#0B1F3A]">{{ $sharedWithMe->count() }}</span>
                        <a href="{{ route('team.prospects.screen', 'shared-with-me') }}" class="text-sm font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">Open</a>
                        <button type="button" x-on:click="expanded = ! expanded" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-300 bg-white text-[#0B1F3A] shadow-sm transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">
                            <span class="sr-only">Toggle Shared With Me</span>
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': expanded }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div x-show="expanded" x-transition>
                <div class="hidden">
                    @forelse ($sharedWithMe as $share)
                        <div class="rounded-lg border border-slate-300 bg-white/85 p-4 shadow-sm">
                            <p class="font-semibold text-[#0B1F3A]">{{ trim($share->first_name.' '.$share->last_name) }}</p>
                            <p class="mt-1 text-sm text-slate-600">Owner: {{ $share->owner_name }}</p>
                            <p class="mt-2 text-xs font-bold uppercase tracking-wide text-purple-700">{{ $share->permission_name ?? 'View Only' }}</p>
                        </div>
                    @empty
                    @endforelse
                </div>

                <div class="mt-5 overflow-x-auto rounded-lg border border-slate-300 bg-white/85">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Prospect</th>
                                <th class="px-4 py-3">Owner</th>
                                <th class="px-4 py-3">Permission</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($sharedWithMe as $share)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-[#0B1F3A]">{{ trim($share->first_name.' '.$share->last_name) }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $share->owner_name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $share->permission_name ?? 'View Only' }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            <div x-data="{ expanded: false }" class="relative rounded-lg border border-slate-400 bg-gradient-to-br from-white via-indigo-50 to-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Shared By Me</h2>
                    <div class="flex items-center gap-3">
                        <span class="absolute right-[10px] top-[10px] rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2.5 py-1 text-xs font-bold text-[#0B1F3A]">{{ $sharedByMe->count() }}</span>
                        <a href="{{ route('team.prospects.screen', 'shared-by-me') }}" class="text-sm font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">Manage</a>
                        <button type="button" x-on:click="expanded = ! expanded" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-300 bg-white text-[#0B1F3A] shadow-sm transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">
                            <span class="sr-only">Toggle Shared By Me</span>
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': expanded }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div x-show="expanded" x-transition>
                <div class="hidden">
                    @forelse ($sharedByMe as $share)
                        <div class="rounded-lg border border-slate-300 bg-white/85 p-4 shadow-sm">
                            <p class="font-semibold text-[#0B1F3A]">{{ trim($share->first_name.' '.$share->last_name) }}</p>
                            <p class="mt-1 text-sm text-slate-600">Collaborator: {{ $share->collaborator_name }}</p>
                            <p class="mt-2 text-xs font-bold uppercase tracking-wide text-indigo-700">{{ $share->permission_name ?? 'View Only' }}</p>
                        </div>
                    @empty
                    @endforelse
                </div>

                <div class="mt-5 overflow-x-auto rounded-lg border border-slate-300 bg-white/85">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Prospect</th>
                                <th class="px-4 py-3">Collaborator</th>
                                <th class="px-4 py-3">Permission</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($sharedByMe as $share)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-[#0B1F3A]">{{ trim($share->first_name.' '.$share->last_name) }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $share->collaborator_name }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $share->permission_name ?? 'View Only' }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>

            <div x-data="{ expanded: false }" class="relative rounded-lg border border-slate-400 bg-gradient-to-br from-white via-amber-50 to-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Import & Duplicates</h2>
                    <div class="flex items-center gap-3">
                        <span class="absolute right-[10px] top-[10px] rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2.5 py-1 text-xs font-bold text-[#0B1F3A]">{{ $importsTable->count() }}</span>
                        <a href="{{ route('team.prospects.screen', 'import') }}" class="text-sm font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">Import</a>
                        <button type="button" x-on:click="expanded = ! expanded" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-300 bg-white text-[#0B1F3A] shadow-sm transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">
                            <span class="sr-only">Toggle Import And Duplicates</span>
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': expanded }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div x-show="expanded" x-transition>
                <div class="hidden">
                    @if ($recentImport)
                        <p class="font-semibold text-[#0B1F3A]">{{ $recentImport->file_name }}</p>
                        <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-lg border border-slate-300 bg-slate-50 p-3">
                                <p class="text-slate-500">Imported</p>
                                <p class="text-xl font-semibold text-[#0B1F3A]">{{ $recentImport->imported_rows }}</p>
                            </div>
                            <div class="rounded-lg border border-slate-300 bg-amber-50 p-3">
                                <p class="text-slate-500">Duplicates</p>
                                <p class="text-xl font-semibold text-[#0B1F3A]">{{ $recentImport->duplicate_rows }}</p>
                            </div>
                        </div>
                        <p class="mt-4 text-sm text-slate-600">Last run {{ \Illuminate\Support\Carbon::parse($recentImport->completed_at ?? $recentImport->updated_at)->diffForHumans() }}</p>
                    @else
                    @endif
                </div>

                <div class="mt-5 overflow-x-auto rounded-lg border border-slate-300 bg-white/85">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">File</th>
                                <th class="px-4 py-3">Imported</th>
                                <th class="px-4 py-3">Duplicates</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse ($importsTable as $import)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-[#0B1F3A]">{{ $import->file_name ?? 'Manual import' }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $import->imported_rows }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $import->duplicate_rows }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ str($import->status)->title() }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1fr_22rem]">
            <div class="overflow-hidden rounded-lg border border-slate-400 bg-gradient-to-br from-white via-slate-50 to-[#FFF9EA] shadow-sm">
                <div class="border-b border-slate-300 bg-white/70 px-6 py-5">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Module Shortcuts</h2>
                </div>
                <div class="grid gap-4 p-6 md:grid-cols-2">
                    @foreach ([
                        ['Add Prospect', 'Create personal prospects with source, tags, interests, and privacy defaults.', 'create'],
                        ['Pipeline Board', 'Kanban board for lead status, priority, interest level, and conversion flow.', 'pipeline'],
                        ['Follow-Up Center', 'Daily follow-ups, overdue tasks, next actions, and completion tracking.', 'follow-ups'],
                        ['Appointment Calendar', 'Scheduled calls, Zoom links, reminders, no-shows, and reschedules.', 'appointments'],
                        ['Access Manager', 'Grant, expire, revoke, and audit prospect sharing permissions.', 'access-manager'],
                        ['Prospect Import', 'CSV preview, duplicate detection, merge/skip/create workflows.', 'import'],
                    ] as [$title, $description, $screen])
                        <a href="{{ $screen === 'create' ? route('team.prospects.create') : route('team.prospects.screen', $screen) }}" class="rounded-lg border border-slate-400 bg-white/80 p-5 shadow-sm transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">
                            <h3 class="font-semibold text-[#0B1F3A]">{{ $title }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $description }}</p>
                        </a>
                    @endforeach
                </div>
            </div>

            <aside class="space-y-6">
                <div class="rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-5 shadow-sm">
                    <h2 class="text-base font-semibold text-[#0B1F3A]">Privacy Rules</h2>
                    <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                        <p>Prospects are private by default and visible only to the owner.</p>
                        <p>Shared users can access only explicitly shared records while access is active and unexpired.</p>
                        <p>Revoked or expired access immediately removes visibility.</p>
                    </div>
                </div>

                <div class="rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-5 shadow-sm">
                    <h2 class="text-base font-semibold text-[#0B1F3A]">Seeded Setup</h2>
                    <div class="mt-4 space-y-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Pipeline</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($pipelineStages->take(6) as $stage)
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $stage->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Types</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($prospectTypes as $type)
                                    <span class="rounded-full bg-[#C8A24A]/15 px-2.5 py-1 text-xs font-semibold text-[#8A6A1F]">{{ $type->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Interests</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($interests as $interest)
                                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">{{ $interest->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        @include('team.partials.prospect-activities-modal')
    </section>
</x-app-layout>
