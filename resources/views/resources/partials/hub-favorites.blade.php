<div class="overflow-hidden rounded-xl border border-[#C8A24A]/30 bg-white shadow-sm">
    <div class="border-b border-[#C8A24A]/20 bg-[#C8A24A]/5 px-5 py-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-semibold text-[#0B1F3A]">My favorites</h2>
                <p class="mt-1 text-xs text-slate-600">Documents you starred in the library.</p>
            </div>
            <a href="{{ route('resources.documents') }}" class="text-xs font-semibold text-[#8A6A1F] hover:underline">Manage</a>
        </div>
    </div>
    <div class="p-4">
        @if ($favorites->isEmpty())
            <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">
                <p class="text-sm font-medium text-[#0B1F3A]">No favorites yet</p>
                <p class="mt-1 text-xs text-slate-500">Star documents in the library to pin them here.</p>
                <a href="{{ route('resources.documents') }}" class="mt-3 inline-flex text-xs font-semibold text-[#8A6A1F] hover:underline">
                    Browse documents →
                </a>
            </div>
        @else
            <ul class="space-y-2">
                @foreach ($favorites as $document)
                    <li>
                        <a
                            href="{{ route('resources.documents', ['document' => $document->id]) }}"
                            class="flex items-center gap-3 rounded-lg border border-slate-200 px-3 py-2.5 transition hover:border-[#C8A24A]/40 hover:bg-[#FFF9EA]"
                        >
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-[#0B1F3A] text-[9px] font-bold uppercase text-[#C8A24A]">
                                {{ $document->resolvedFormat() }}
                            </span>
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-semibold text-[#0B1F3A]">{{ $document->title }}</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
