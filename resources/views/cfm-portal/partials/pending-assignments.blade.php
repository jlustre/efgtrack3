@if (! empty($profile['pendingAssignmentRows']))
    <div class="rounded-xl border border-[#C8A24A]/30 bg-[#FFF9EA] p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-[#0B1F3A]">Pending Assignments</h3>
                <p class="mt-1 text-xs text-slate-600">Confirm new trainees assigned by agency leadership.</p>
            </div>
            <span class="rounded-full bg-[#C8A24A]/20 px-3 py-1 text-xs font-semibold text-[#8A6A1F]">{{ count($profile['pendingAssignmentRows']) }} awaiting</span>
        </div>

        <div class="space-y-3">
            @foreach ($profile['pendingAssignmentRows'] as $row)
                <div class="flex flex-col gap-3 rounded-lg border border-slate-200 bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="font-semibold text-[#0B1F3A]">{{ $row['name'] }}</p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $row['rank'] }} · Assigned by {{ $row['assignedBy'] }} · {{ $row['startedAt'] }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('cfm.portal.assignments.confirm', $row['id']) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-[#C8A24A] px-4 py-2 text-sm font-bold text-[#0B1F3A] transition hover:bg-[#D8B85F]">
                            Confirm Assignment
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@endif
