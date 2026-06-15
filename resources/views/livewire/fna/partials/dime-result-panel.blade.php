@props(['result' => []])

@php
    $gap = (float) ($result['estimated_protection_gap'] ?? 0);
    $need = (float) ($result['total_dime_need'] ?? 0);
    $existing = (float) ($result['existing_life_insurance'] ?? 0) + (float) ($result['liquid_assets_allocated'] ?? 0);
    $pct = $need > 0 ? min(100, round(($existing / $need) * 100)) : 0;
@endphp

<div class="rounded-xl border border-[#C8A24A]/40 bg-[#FFF9EA] p-5">
    <h3 class="text-lg font-semibold text-[#0B1F3A]">DIME Results</h3>

    <dl class="mt-4 space-y-2 text-sm">
        <div class="flex justify-between"><dt class="text-slate-600">Total Debt (D)</dt><dd class="font-semibold">${{ number_format($result['total_debt'] ?? 0, 0) }}</dd></div>
        <div class="flex justify-between"><dt class="text-slate-600">Income Need (I)</dt><dd class="font-semibold">${{ number_format($result['total_income_need'] ?? 0, 0) }}</dd></div>
        <div class="flex justify-between"><dt class="text-slate-600">Mortgage Need (M)</dt><dd class="font-semibold">${{ number_format($result['total_mortgage_need'] ?? 0, 0) }}</dd></div>
        <div class="flex justify-between"><dt class="text-slate-600">Education Need (E)</dt><dd class="font-semibold">${{ number_format($result['total_education_need'] ?? 0, 0) }}</dd></div>
        <div class="flex justify-between border-t border-[#C8A24A]/30 pt-2"><dt class="font-semibold text-[#0B1F3A]">Total DIME Need</dt><dd class="font-bold text-[#0B1F3A]">${{ number_format($need, 0) }}</dd></div>
        <div class="flex justify-between"><dt class="text-slate-600">Existing Coverage + Assets</dt><dd class="font-semibold">${{ number_format($existing, 0) }}</dd></div>
        <div class="flex justify-between"><dt class="font-semibold text-red-700">Protection Gap</dt><dd class="font-bold text-red-700">${{ number_format($gap, 0) }}</dd></div>
        <div class="flex justify-between"><dt class="text-slate-600">Recommended Range</dt><dd class="font-semibold">${{ number_format($result['recommended_coverage_min'] ?? 0, 0) }} – ${{ number_format($result['recommended_coverage_max'] ?? 0, 0) }}</dd></div>
    </dl>

    <div class="mt-4">
        <div class="flex justify-between text-xs text-slate-500"><span>Coverage vs need</span><span>{{ $pct }}%</span></div>
        <div class="mt-1 h-3 overflow-hidden rounded-full bg-slate-200">
            <div class="h-full bg-[#C8A24A]" style="width: {{ $pct }}%"></div>
        </div>
    </div>

    <p class="mt-4 text-xs leading-5 text-slate-600">{{ config('fna.dime_disclaimer') }}</p>

    @if (! empty($gapSummary))
        <div class="mt-4 rounded-lg border border-[#0B1F3A]/20 bg-white p-4">
            <h4 class="text-sm font-semibold text-[#0B1F3A]">Protection Gap Summary</h4>
            <p class="mt-2 text-sm leading-6 text-slate-700">{{ $gapSummary }}</p>
            @if (! empty($complianceNotice))
                <p class="mt-2 text-xs leading-5 text-slate-500">{{ $complianceNotice }}</p>
            @endif
        </div>
    @endif
</div>
