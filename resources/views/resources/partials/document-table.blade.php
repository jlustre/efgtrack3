@php
    $showFeaturedBadge = $showFeaturedBadge ?? false;
@endphp

<div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-4 py-3">Document</th>
                <th class="px-4 py-3">Category</th>
                <th class="hidden sm:table-cell px-4 py-3">Format</th>
                <th class="hidden lg:table-cell px-4 py-3">Description</th>
                <th class="hidden md:table-cell px-4 py-3">Updated</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse ($documents as $document)
                @php
                    $categoryKey = $document->category ?: 'general';
                    $categoryMeta = $categories[$categoryKey] ?? $categories['general'];
                    $format = $document->resolvedFormat();
                    $actionUrl = $document->resolvedAccessUrl();
                    $formUrl = $document->formUrl();
                @endphp
                <tr class="transition hover:bg-[#FFF9EA]/40">
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($showFeaturedBadge && $document->is_featured)
                                <span class="rounded-full bg-[#C8A24A]/15 px-2 py-0.5 text-[0.65rem] font-bold uppercase tracking-wide text-[#8A6A1F]">
                                    Featured
                                </span>
                            @endif
                        </div>
                        @if ($document->isInteractiveForm() && $formUrl)
                            <a
                                href="{{ $formUrl }}"
                                class="font-semibold text-[#0B1F3A] transition hover:text-[#C8A24A]"
                            >
                                {{ $document->title }}
                            </a>
                        @else
                            <button
                                type="button"
                                @click="openPreview({{ $document->id }})"
                                class="text-left font-semibold text-[#0B1F3A] transition hover:text-[#C8A24A]"
                            >
                                {{ $document->title }}
                            </button>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full border px-2 py-0.5 text-[0.65rem] font-bold uppercase tracking-wide {{ $categoryMeta['accent'] }}">
                            {{ $categoryMeta['label'] }}
                        </span>
                    </td>
                    <td class="hidden sm:table-cell px-4 py-3">
                        <span class="inline-flex rounded-md border border-slate-200 bg-slate-50 px-2 py-0.5 text-xs font-bold text-[#0B1F3A]">
                            {{ $format }}
                        </span>
                    </td>
                    <td class="hidden lg:table-cell px-4 py-3 text-slate-600">
                        <span class="line-clamp-2">{{ $document->description ?: 'No description provided.' }}</span>
                    </td>
                    <td class="hidden md:table-cell px-4 py-3 text-slate-500">
                        {{ $document->updated_at?->format('M j, Y') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-2">
                            @if ($document->isInteractiveForm() && $formUrl)
                                <a
                                    href="{{ $formUrl }}"
                                    class="inline-flex items-center rounded-md bg-[#0B1F3A] px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-[#13345f]"
                                >
                                    Fill Form
                                </a>
                            @else
                                <button
                                    type="button"
                                    @click="openPreview({{ $document->id }})"
                                    class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-[#0B1F3A] transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]"
                                >
                                    View
                                </button>
                                @if ($actionUrl)
                                    <a
                                        href="{{ $actionUrl }}"
                                        @if (! $document->hasDownloadableFile()) target="_blank" rel="noopener noreferrer" @endif
                                        class="inline-flex items-center rounded-md bg-[#0B1F3A] px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-[#13345f]"
                                    >
                                        {{ $document->hasDownloadableFile() ? 'Download' : 'Open' }}
                                    </a>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">
                        No documents to display.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
