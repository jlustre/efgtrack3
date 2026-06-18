@props(['text'])

@if (filled($text))
    <span {{ $attributes->merge([
        'class' => 'group relative inline-flex h-6 w-6 shrink-0 cursor-pointer items-center justify-center rounded-full border border-[#C8A24A]/35 bg-[#FFF9EA] text-xs font-bold text-[#8A6A1F] shadow-sm',
    ]) }}>
        ?
        <span class="pointer-events-none absolute left-1/2 top-7 z-20 hidden w-72 -translate-x-1/2 rounded-md bg-[#0B1F3A] px-3 py-2 text-left text-xs font-medium leading-5 text-white shadow-lg group-hover:block">
            {{ $text }}
        </span>
    </span>
@endif
