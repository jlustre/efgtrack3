<div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-amber-400">My Trainees</h3>
        <span class="text-xs text-gray-500">{{ $profile['activeApprentices'] }} active · {{ $profile['completedApprentices'] }} completed</span>
    </div>

    @if (count($profile['apprentices']) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-800">
                        <th class="pb-2 font-medium">Name</th>
                        <th class="pb-2 font-medium">Rank</th>
                        <th class="pb-2 font-medium">Checklist</th>
                        <th class="pb-2 font-medium">Status</th>
                        <th class="pb-2 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @foreach ($profile['apprentices'] as $trainee)
                        <tr>
                            <td class="py-2.5 text-white">{{ $trainee['name'] }}</td>
                            <td class="py-2.5 text-gray-400">{{ $trainee['rank'] }}</td>
                            <td class="py-2.5">
                                @if (! empty($trainee['assignmentId']))
                                    <div class="flex items-center gap-2">
                                        <div class="h-1.5 w-16 overflow-hidden rounded-full bg-gray-800">
                                            <div class="h-full rounded-full bg-amber-500" style="width: {{ $trainee['checklistPercent'] ?? 0 }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $trainee['checklistPercent'] ?? 0 }}%</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="py-2.5">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-gray-800 text-gray-300">
                                    {{ ucfirst($trainee['status']) }}
                                </span>
                            </td>
                            <td class="py-2.5 text-right space-x-2">
                                @if (! empty($trainee['assignmentId']))
                                    <button
                                        type="button"
                                        class="inline-flex rounded-lg border border-gray-700 px-3 py-1.5 text-xs font-semibold text-gray-200 transition hover:border-amber-500 hover:text-amber-400"
                                        @click="openTraineeChecklistModal(@js(route('cfm.portal.trainees.checklist', $trainee['assignmentId'])))"
                                    >
                                        View checklist
                                    </button>
                                    <a
                                        href="{{ route('cfm.portal.trainees.checklist', $trainee['assignmentId']) }}"
                                        class="inline-flex rounded-lg border border-gray-700 px-3 py-1.5 text-xs font-semibold text-gray-200 transition hover:border-amber-500 hover:text-amber-400"
                                    >
                                        Track mentoring
                                    </a>
                                @endif
                                @if (! empty($trainee['needsFirstContact']) && ! empty($trainee['assignmentId']))
                                    <form method="POST" action="{{ route('cfm.portal.assignments.first-contact', $trainee['assignmentId']) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-bold text-black transition hover:bg-amber-400">
                                            Send first email
                                        </button>
                                    </form>
                                @elseif (! empty($trainee['assignmentId']) && empty($trainee['needsFirstContact']))
                                    <span class="text-xs text-emerald-400">Intro sent</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-sm text-gray-500">No active trainees assigned yet. Confirm pending assignments above to activate trainees.</p>
    @endif

    @if (! empty($profile['apprenticeBreakdown']))
        <dl class="mt-5 grid grid-cols-2 gap-2 text-xs border-t border-gray-800 pt-4">
            @foreach ($profile['apprenticeBreakdown'] as $key => $value)
                <div class="flex justify-between rounded-lg bg-gray-800/50 px-2 py-1.5">
                    <span class="text-gray-500">{{ str($key)->headline() }}</span>
                    <span class="text-white font-medium">{{ $value }}</span>
                </div>
            @endforeach
        </dl>
    @endif
</div>
