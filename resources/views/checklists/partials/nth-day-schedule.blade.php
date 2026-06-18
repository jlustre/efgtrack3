@if ($step->nth_day)
    <span class="rounded-full bg-sky-50 px-2 py-0.5 text-xs font-semibold text-sky-700">Day {{ $step->nth_day }}</span>
@endif
@if (! empty($step->expected_due_date))
    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">Due {{ $step->expected_due_date->format('M j, Y') }}</span>
@endif
