@props(['title', 'description' => null, 'innerClass' => 'grid grid-cols-1 gap-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4'])

<section {{ $attributes->merge(['class' => 'rounded-lg border border-slate-200 bg-white p-2.5 shadow-sm sm:p-3']) }}>
    <header class="mb-2 border-b border-slate-100 pb-1.5">
        <h3 class="text-[11px] font-bold uppercase tracking-wider text-[#0B1F3A]">{{ $title }}</h3>
        @if ($description)
            <p class="mt-0.5 text-[11px] leading-tight text-slate-500">{{ $description }}</p>
        @endif
    </header>
    <div class="{{ $innerClass }}">
        {{ $slot }}
    </div>
</section>
