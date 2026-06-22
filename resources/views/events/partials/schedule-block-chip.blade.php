@props(['block', 'compact' => false, 'showOwner' => false])

@php
    $color = $block['color'] ?? '#94A3B8';
    $startsAt = $block['starts_at'];
    $endsAt = $block['ends_at'];
@endphp

<div
    {{ $attributes->merge(['class' => 'rounded-md border border-dashed px-2 py-1.5 text-left']) }}
    style="background-color: {{ $color }}14; border-color: {{ $color }}88;"
    title="{{ $block['label'] ?? 'Blocked' }}"
>
    <div class="flex items-center gap-1.5">
        <span class="h-2 w-2 shrink-0 rounded-full" style="background-color: {{ $color }}"></span>
        <span class="truncate text-xs font-semibold text-[#0B1F3A]">{{ $block['label'] ?? 'Blocked' }}</span>
    </div>
    @unless ($compact)
        <div class="mt-0.5 text-[0.65rem] font-medium text-slate-500">
            @if ($block['is_all_day'] ?? false)
                All day · Unavailable
            @else
                {{ $startsAt->format('g:i A') }} – {{ $endsAt->format('g:i A') }}
            @endif
            @if ($showOwner && ! empty($block['owner_name']))
                · {{ $block['owner_name'] }}
            @endif
        </div>
    @endunless
</div>
