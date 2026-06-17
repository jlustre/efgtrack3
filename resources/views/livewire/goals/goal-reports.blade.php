<div class="rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-200 p-4">
        <h2 class="text-lg font-semibold text-[#0B1F3A]">Performance Reports</h2>
        <p class="text-sm text-slate-600">Download or email PDF summaries of your goal progress.</p>
    </div>

    <div class="flex flex-wrap items-end gap-4 border-b border-slate-100 bg-slate-50/80 p-4">
        <div>
            <label for="report-period" class="text-xs font-semibold uppercase text-slate-500">Report period</label>
            <select
                id="report-period"
                wire:model.live="periodType"
                class="mt-1 block rounded-md border-gray-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
            >
                <option value="weekly">Last week</option>
                <option value="monthly">Last month</option>
                <option value="quarterly">Last quarter</option>
                <option value="annual">Last year</option>
            </select>
        </div>

        <a
            href="{{ route('goals.reports.download', ['period' => $periodType]) }}"
            wire:key="download-{{ $periodType }}"
            class="inline-flex items-center rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#132F55]"
        >
            Download PDF
        </a>

        <form method="POST" action="{{ route('goals.reports.email') }}" class="inline" wire:key="email-{{ $periodType }}">
            @csrf
            <input type="hidden" name="period" value="{{ $periodType }}">
            <button
                type="submit"
                class="rounded-lg border border-[#C8A24A] bg-[#FFF9EA] px-4 py-2 text-sm font-semibold text-[#8A6A1F] hover:bg-[#F7E8B8]"
            >
                Email report
            </button>
        </form>
    </div>

    <div class="grid gap-4 p-4 sm:grid-cols-4">
        <div class="rounded-lg bg-slate-50 p-4 text-center">
            <p class="text-2xl font-bold text-[#0B1F3A]">{{ $preview['average_progress'] }}%</p>
            <p class="text-xs uppercase text-slate-500">Avg progress</p>
        </div>
        <div class="rounded-lg bg-slate-50 p-4 text-center">
            <p class="text-2xl font-bold text-[#0B1F3A]">{{ $preview['goals']->count() }}</p>
            <p class="text-xs uppercase text-slate-500">Goals</p>
        </div>
        <div class="rounded-lg bg-slate-50 p-4 text-center">
            <p class="text-2xl font-bold text-emerald-700">{{ $preview['completed_count'] }}</p>
            <p class="text-xs uppercase text-slate-500">Completed</p>
        </div>
        <div class="rounded-lg bg-slate-50 p-4 text-center">
            <p class="text-2xl font-bold text-amber-700">{{ $preview['off_track_count'] }}</p>
            <p class="text-xs uppercase text-slate-500">Off track</p>
        </div>
    </div>

    <div class="border-t border-slate-100 p-4">
        <p class="text-xs text-slate-500">
            {{ $preview['period_label'] }} report:
            {{ $preview['period_start']->format('M j, Y') }} – {{ $preview['period_end']->format('M j, Y') }}
        </p>

        @if ($preview['goals']->isNotEmpty())
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-3 py-2">Goal</th>
                            <th class="px-3 py-2">Category</th>
                            <th class="px-3 py-2">Progress</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Deadline</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($preview['goals'] as $goal)
                            <tr>
                                <td class="px-3 py-2 font-medium text-[#0B1F3A]">{{ $goal->name }}</td>
                                <td class="px-3 py-2 text-slate-600">{{ $goal->category?->name ?? '—' }}</td>
                                <td class="px-3 py-2 text-slate-600">{{ $goal->progressPercent() }}%</td>
                                <td class="px-3 py-2 text-slate-600">{{ config('goals.statuses.'.$goal->status, $goal->status) }}</td>
                                <td class="px-3 py-2 text-slate-600">{{ $goal->deadline_at?->format('M j, Y') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="mt-4 text-sm text-slate-500">No goals found for this reporting period.</p>
        @endif
    </div>
</div>
