@php
    $quickLinks = $home['quick_links'] ?? [];
    $links = $quickLinks['links'] ?? collect();
    $categories = $quickLinks['categories'] ?? [];
    $libraryUrl = $quickLinks['library_url'] ?? null;

    $cardClasses = 'group flex w-full items-start gap-3 rounded-lg border border-slate-300 bg-slate-100 p-4 text-left shadow-sm transition hover:border-[#C8A24A]/50 hover:bg-[#FFF9EA]';
@endphp

@if ($links->isNotEmpty())
    <section>
        <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Quick Links</h2>
                <p class="mt-1 text-sm text-slate-500">Zoom rooms, team calls, training sessions, and other quick-access URLs.</p>
            </div>

            @if ($libraryUrl)
                <a
                    href="{{ $libraryUrl }}"
                    class="inline-flex items-center gap-1 text-sm font-semibold text-[#0B1F3A] transition hover:text-[#C8A24A]"
                >
                    View all links
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            @endif
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($links as $link)
                @php
                    $categoryKey = $link->category ?: 'general';
                    $categoryMeta = $categories[$categoryKey] ?? $categories['general'] ?? ['label' => 'General', 'accent' => 'bg-slate-100 text-slate-700 border-slate-200'];
                    $linkUrl = $link->resolvedAccessUrl();
                    $linkHost = $linkUrl ? parse_url($linkUrl, PHP_URL_HOST) : null;
                @endphp

                @if ($linkUrl)
                    <a
                        href="{{ $linkUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="{{ $cardClasses }}"
                    >
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#0B1F3A]/10 text-[#0B1F3A] transition group-hover:bg-[#C8A24A]/25">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </span>

                        <span class="min-w-0">
                            <span class="flex flex-wrap items-center gap-1.5">
                                <span class="block text-sm font-semibold text-[#0B1F3A] group-hover:text-[#C8A24A]">{{ $link->title }}</span>
                                @if ($link->is_featured)
                                    <span class="rounded-full bg-[#C8A24A]/15 px-1.5 py-0.5 text-[0.6rem] font-bold uppercase tracking-wide text-[#8A6A1F]">Featured</span>
                                @endif
                            </span>

                            <span class="mt-0.5 block text-xs text-slate-500">
                                @if (filled($link->description))
                                    {{ str($link->description)->limit(72) }}
                                @elseif ($linkHost)
                                    {{ $linkHost }}
                                @else
                                    {{ $categoryMeta['label'] }}
                                @endif
                            </span>

                            <span class="mt-1.5 inline-flex rounded-full border px-2 py-0.5 text-[0.65rem] font-bold uppercase tracking-wide {{ $categoryMeta['accent'] }}">
                                {{ $categoryMeta['label'] }}
                            </span>
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    </section>
@endif
