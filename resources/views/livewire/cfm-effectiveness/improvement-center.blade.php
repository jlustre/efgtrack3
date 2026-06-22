<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Improvement Center</h2>
            <p class="text-sm text-slate-600">Action plans generated from objective metrics and trainee feedback trends.</p>
        </div>
        @can('manage action plans')
            <button wire:click="generatePlans" class="rounded-lg bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#FFF9EA]">Generate Action Plans</button>
        @endcan
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white/90 p-6 shadow-sm">
            <h3 class="font-semibold text-[#0B1F3A]">Recommendations</h3>
            <ul class="mt-4 space-y-3">
                @forelse ($recommendations as $rec)
                    <li class="rounded-lg border border-[#C8A24A]/20 bg-[#FFF9EA]/40 px-3 py-2 text-sm">
                        <p class="font-semibold text-[#0B1F3A]">{{ $rec['area'] }} <span class="text-xs uppercase text-slate-500">({{ $rec['priority'] }})</span></p>
                        <p class="mt-1 text-slate-600">{{ $rec['suggestion'] }}</p>
                    </li>
                @empty
                    <li class="text-sm text-slate-600">No active improvement areas identified.</li>
                @endforelse
            </ul>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white/90 p-6 shadow-sm">
            <h3 class="font-semibold text-[#0B1F3A]">Active Action Plans</h3>
            <div class="mt-4 space-y-4">
                @forelse ($actionPlans as $plan)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="font-semibold text-[#0B1F3A]">{{ $plan->improvement_area }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $plan->target_outcome }}</p>
                        <div class="mt-3 h-2 rounded-full bg-slate-200">
                            <div class="h-2 rounded-full bg-[#C8A24A]" style="width: {{ $plan->progress }}%"></div>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ $plan->progress }}% complete @if($plan->due_date) · Due {{ $plan->due_date->format('M j, Y') }} @endif</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">No active action plans.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
