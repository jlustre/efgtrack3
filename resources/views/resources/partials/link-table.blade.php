@php
    $showFeaturedBadge = $showFeaturedBadge ?? false;
@endphp

<div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-4 py-3">Link</th>
                <th class="px-4 py-3">Category</th>
                <th class="hidden lg:table-cell px-4 py-3">Description</th>
                <th class="hidden md:table-cell px-4 py-3">Updated</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse ($links as $link)
                @php
                    $categoryKey = $link->category ?: 'general';
                    $categoryMeta = $categories[$categoryKey] ?? $categories['general'];
                    $linkUrl = $link->resolvedAccessUrl();
                    $linkHost = $linkUrl ? parse_url($linkUrl, PHP_URL_HOST) : null;
                @endphp
                <tr class="transition hover:bg-[#FFF9EA]/40" x-data="{ copied: false, copyLink() { if (! @js($linkUrl)) return; navigator.clipboard.writeText(@js($linkUrl)).then(() => { this.copied = true; setTimeout(() => this.copied = false, 2000); }); } }">
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($showFeaturedBadge && $link->is_featured)
                                <span class="rounded-full bg-[#C8A24A]/15 px-2 py-0.5 text-[0.65rem] font-bold uppercase tracking-wide text-[#8A6A1F]">
                                    Featured
                                </span>
                            @endif
                        </div>
                        @if ($linkUrl)
                            <a
                                href="{{ $linkUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="font-semibold text-[#0B1F3A] transition hover:text-[#C8A24A]"
                            >
                                {{ $link->title }}
                            </a>
                        @else
                            <span class="font-semibold text-[#0B1F3A]">{{ $link->title }}</span>
                        @endif
                        @if ($linkHost)
                            <div class="mt-0.5 truncate text-xs text-slate-500">{{ $linkHost }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full border px-2 py-0.5 text-[0.65rem] font-bold uppercase tracking-wide {{ $categoryMeta['accent'] }}">
                            {{ $categoryMeta['label'] }}
                        </span>
                    </td>
                    <td class="hidden lg:table-cell px-4 py-3 text-slate-600">
                        <span class="line-clamp-2">{{ $link->description ?: 'No description provided.' }}</span>
                    </td>
                    <td class="hidden md:table-cell px-4 py-3 text-slate-500">
                        {{ $link->updated_at?->format('M j, Y') }}
                    </td>
                    <td class="px-4 py-3">
                        @if ($linkUrl)
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    type="button"
                                    @click="copyLink()"
                                    class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-[#0B1F3A] transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]"
                                >
                                    <span x-text="copied ? 'Copied' : 'Copy'"></span>
                                </button>
                                <a
                                    href="{{ $linkUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center rounded-md bg-[#0B1F3A] px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-[#13345f]"
                                >
                                    Open
                                </a>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">
                        No links to display.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
