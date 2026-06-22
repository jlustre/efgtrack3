<div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-4 py-3">
        <p class="text-xs font-semibold uppercase tracking-wide text-[#8A6A1F]">Quick navigation</p>
    </div>
    <div class="flex gap-2 overflow-x-auto p-3">
        @foreach ([
            ['Library Home', route('resources.index'), request()->routeIs('resources.index')],
            ['Documents', route('resources.documents'), request()->routeIs('resources.documents')],
            ['Links', route('resources.links'), request()->routeIs('resources.links')],
            ['Videos', route('resources.videos'), request()->routeIs('resources.videos')],
            ['Webinars', route('resources.recorded-webinars'), request()->routeIs('resources.recorded-webinars')],
            ['Associate Agreement', route('resources.forms.associate-participation-agreement'), request()->routeIs('resources.forms.*')],
        ] as [$label, $url, $active])
            <a
                href="{{ $url }}"
                @class([
                    'shrink-0 rounded-lg border px-3 py-2 text-sm font-semibold transition',
                    'border-[#0B1F3A] bg-[#0B1F3A] text-white' => $active,
                    'border-slate-200 bg-slate-50 text-[#0B1F3A] hover:border-[#C8A24A]/50 hover:bg-[#FFF9EA]' => ! $active,
                ])
            >
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>
