<div>
    <div class="flex items-center justify-between text-xs font-semibold text-slate-600">
        <span>{{ $label }}</span>
        <span>{{ $value }}%</span>
    </div>
    <div class="mt-1 h-2 overflow-hidden rounded-full bg-slate-200">
        <div class="h-full rounded-full bg-[#C8A24A]" style="width: {{ $value }}%"></div>
    </div>
</div>
