@props([
    'href',
    'label',
    'tooltip',
    'variant' => 'secondary',
])

@php
    $linkClass = $variant === 'primary'
        ? 'inline-flex items-center rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B85F]'
        : 'inline-flex items-center rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10';
@endphp

<span {{ $attributes->merge(['class' => 'group relative inline-flex']) }}>
    <a href="{{ $href }}" class="{{ $linkClass }}">{{ $label }}</a>
    <span
        role="tooltip"
        class="pointer-events-none absolute left-1/2 top-full z-30 mt-2 w-64 max-w-[calc(100vw-2rem)] -translate-x-1/2 rounded-lg border border-[#C8A24A] bg-[#FFF9EA] px-3 py-2.5 text-left text-xs font-medium leading-5 text-[#0B1F3A] opacity-0 shadow-[0_8px_24px_rgba(11,31,58,0.28)] ring-1 ring-[#0B1F3A]/15 transition duration-150 group-hover:opacity-100 group-focus-within:opacity-100"
    >{{ $tooltip }}</span>
</span>
