<section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-5 py-4">
        <h2 class="text-lg font-semibold text-[#0B1F3A]">Browse the library</h2>
        <p class="mt-1 text-sm text-slate-600">Jump into the resource type you need right now.</p>
    </div>
    <div class="grid gap-3 p-5 sm:grid-cols-2">
        @foreach ($librarySections as $section)
            <a
                href="{{ $section['route'] }}"
                class="group relative overflow-hidden rounded-xl border border-slate-200 bg-slate-50/80 p-5 transition hover:border-[#C8A24A]/50 hover:bg-[#FFF9EA] hover:shadow-sm"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-semibold text-[#0B1F3A] group-hover:text-[#8A6A1F]">{{ $section['title'] }}</h3>
                            @if ($section['status'])
                                <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-600 ring-1 ring-slate-200">
                                    {{ $section['status'] }}
                                </span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $section['description'] }}</p>
                    </div>
                    <span class="shrink-0 rounded-full bg-[#0B1F3A]/10 px-2.5 py-1 text-xs font-bold tabular-nums text-[#0B1F3A]">
                        {{ $section['count'] }}
                    </span>
                </div>
                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-[#8A6A1F]">Open section →</p>
            </a>
        @endforeach
    </div>
</section>
