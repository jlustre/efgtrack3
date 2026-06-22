<section class="rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-5 py-4">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-[#0B1F3A]">Prospect directory</h2>
                <p class="mt-1 text-sm text-slate-600">{{ $allProspects->total() }} total · search, filter, and manage your CRM records</p>
            </div>
            <a href="{{ route('team.prospects.create') }}" class="inline-flex items-center justify-center rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#132F55]">
                Add prospect
            </a>
        </div>

        <form method="GET" action="{{ route('team.prospects') }}" class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-6">
            <label class="block xl:col-span-2">
                <span class="sr-only">Search prospects</span>
                <input
                    type="search"
                    name="prospect_search"
                    value="{{ request('prospect_search') }}"
                    placeholder="Search name, email, phone, city..."
                    class="block h-10 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
                >
            </label>
            <label class="block">
                <span class="sr-only">Status</span>
                <select name="prospect_status" class="block h-10 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    <option value="">All statuses</option>
                    @foreach ($prospectStatuses as $status)
                        <option value="{{ $status }}" @selected(request('prospect_status') === $status)>{{ str($status)->title() }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="sr-only">Pipeline stage</span>
                <select name="prospect_stage" class="block h-10 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    <option value="">All stages</option>
                    @foreach ($pipelineStages as $stage)
                        <option value="{{ $stage->id }}" @selected((string) request('prospect_stage') === (string) $stage->id)>{{ $stage->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="sr-only">Source</span>
                <select name="prospect_source" class="block h-10 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    <option value="">All sources</option>
                    @foreach ($prospectSources as $source)
                        <option value="{{ $source->id }}" @selected((string) request('prospect_source') === (string) $source->id)>{{ $source->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="sr-only">Interest</span>
                <select name="prospect_interest" class="block h-10 w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    <option value="">All interest</option>
                    @foreach (['cold', 'warm', 'hot'] as $interest)
                        <option value="{{ $interest }}" @selected(request('prospect_interest') === $interest)>{{ str($interest)->title() }}</option>
                    @endforeach
                </select>
            </label>
            <div class="flex gap-2 xl:col-span-6">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#132F55]">
                    Apply filters
                </button>
                <a href="{{ route('team.prospects') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Prospect</th>
                    <th class="px-4 py-3">Contact</th>
                    <th class="px-4 py-3">Stage</th>
                    <th class="px-4 py-3">Interest</th>
                    <th class="px-4 py-3">Next follow-up</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($allProspects as $prospect)
                    <tr class="transition hover:bg-[#FFF9EA]/40">
                        <td class="px-4 py-3">
                            <a href="{{ route('team.prospects.records.show', $prospect) }}" class="font-semibold text-[#0B1F3A] hover:text-[#8A6A1F]">
                                {{ $prospect->first_name }} {{ $prospect->last_name }}
                            </a>
                            <p class="text-xs text-slate-500">{{ $prospect->city ?? 'City not set' }} · {{ str($prospect->status)->title() }}</p>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            <div>{{ $prospect->email ?? '—' }}</div>
                            <div class="text-xs text-slate-500">{{ $prospect->phone ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $prospect->stage?->name ?? 'No stage' }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'rounded-full px-2 py-0.5 text-xs font-semibold',
                                'bg-red-100 text-red-700' => $prospect->interest_level === 'hot',
                                'bg-amber-100 text-amber-800' => $prospect->interest_level === 'warm',
                                'bg-slate-100 text-slate-700' => $prospect->interest_level === 'cold',
                            ])>{{ str($prospect->interest_level)->title() }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $prospect->next_follow_up_at?->format('M j, g:i A') ?? 'Not scheduled' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('team.prospects.records.show', $prospect) }}" title="View" class="rounded p-1 text-sky-600 hover:bg-sky-50 hover:text-sky-700">
                                    <span class="sr-only">View</span>
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <button type="button" title="Log activity" class="rounded p-1 text-[#8A6A1F] hover:bg-[#FFF9EA] hover:text-[#0B1F3A]" x-on:click="openFor({ id: @js($prospect->id), name: @js(trim($prospect->first_name.' '.$prospect->last_name)) })">
                                    <span class="sr-only">Activities</span>
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                                </button>
                                <a href="{{ route('team.prospects.records.edit', $prospect) }}" title="Edit" class="rounded p-1 text-indigo-600 hover:bg-indigo-50 hover:text-indigo-700">
                                    <span class="sr-only">Edit</span>
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('team.prospects.records.archive', $prospect) }}" onsubmit="return confirm('Archive this prospect?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" title="Archive" class="rounded p-1 text-violet-600 hover:bg-violet-50 hover:text-violet-700">
                                        <span class="sr-only">Archive</span>
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 8v13H3V8M1 3h22v5H1zM10 12h4"/></svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('team.prospects.records.destroy', $prospect) }}" onsubmit="return confirm('Permanently delete this prospect?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Delete" class="rounded p-1 text-red-600 hover:bg-red-50 hover:text-red-700">
                                        <span class="sr-only">Delete</span>
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                            No prospects match your filters.
                            <a href="{{ route('team.prospects.create') }}" class="ml-1 font-semibold text-[#8A6A1F] hover:underline">Add your first prospect</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="border-t border-slate-100 px-5 py-4">
        {{ $allProspects->links() }}
    </div>
</section>
