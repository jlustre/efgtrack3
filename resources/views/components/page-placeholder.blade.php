@props([
    'title',
    'subtitle' => null,
])

<section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Phase 0 Scaffold</p>
    <h1 class="mt-2 text-2xl font-semibold text-[#0B1F3A]">{{ $title }}</h1>

    @if ($subtitle)
        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">{{ $subtitle }}</p>
    @endif
</section>
