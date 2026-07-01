@php
    $item = $item ?? [];
@endphp

<div class="flex items-start justify-between gap-3">
    <div class="min-w-0 flex-1">
        <p class="truncate text-sm font-semibold text-[#0B1F3A] group-hover:text-[#C8A24A]">
            {{ $item['title'] ?? 'Untitled' }}
        </p>

        @if (filled($item['subtitle'] ?? null))
            <p class="mt-0.5 truncate text-xs text-slate-600">{{ $item['subtitle'] }}</p>
        @endif

        @if (! is_null($item['progress'] ?? null))
            <div class="mt-2">
                <div class="mb-1 flex items-center justify-between text-[0.68rem] font-semibold text-slate-500">
                    <span>Progress</span>
                    <span class="text-[#0B1F3A]">{{ (int) $item['progress'] }}%</span>
                </div>
                <div class="h-1.5 overflow-hidden rounded-full bg-slate-200">
                    <div
                        class="h-1.5 rounded-full bg-[#C8A24A] transition-all duration-300"
                        style="width: {{ max(0, min(100, (int) ($item['progress'] ?? 0))) }}%"
                    ></div>
                </div>
            </div>
        @endif

        @if (filled($item['meta'] ?? null))
            <p class="mt-1 text-[0.68rem] font-medium uppercase tracking-wide text-slate-400">{{ $item['meta'] }}</p>
        @endif
    </div>

    @if (filled($item['badge'] ?? null))
        <span @class([
            'shrink-0 rounded-full px-2 py-0.5 text-[0.68rem] font-semibold uppercase tracking-wide',
            ($item['highlight'] ?? false) ? 'bg-[#FFF9EA] text-[#8A6A1F]' : 'bg-slate-100 text-slate-600',
        ])>{{ $item['badge'] }}</span>
    @endif
</div>
