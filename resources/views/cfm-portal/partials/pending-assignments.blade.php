@if (! empty($profile['pendingAssignmentRows']))
    <div class="bg-amber-900/20 backdrop-blur-sm border border-amber-500/30 rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-amber-300">Pending Assignments</h3>
                <p class="text-xs text-amber-100/70 mt-1">Confirm new trainees assigned by agency leadership.</p>
            </div>
            <span class="rounded-full bg-amber-500/20 px-3 py-1 text-xs font-semibold text-amber-200">{{ count($profile['pendingAssignmentRows']) }} awaiting</span>
        </div>

        <div class="space-y-3">
            @foreach ($profile['pendingAssignmentRows'] as $row)
                <div class="flex flex-col gap-3 rounded-xl border border-amber-500/20 bg-black/30 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold text-white">{{ $row['name'] }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $row['rank'] }} · Assigned by {{ $row['assignedBy'] }} · {{ $row['startedAt'] }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('cfm.portal.assignments.confirm', $row['id']) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-amber-500 px-4 py-2 text-sm font-bold text-black transition hover:bg-amber-400">
                            Confirm Assignment
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@endif
