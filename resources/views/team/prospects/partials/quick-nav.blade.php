<div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
    <div class="border-b border-slate-100 px-4 py-3">
        <p class="text-xs font-semibold uppercase tracking-wide text-[#8A6A1F]">Quick navigation</p>
    </div>
    <div class="flex gap-2 overflow-x-auto p-3">
        @foreach ([
            ['Recruiting Pipeline', route('team.recruiting.index'), true],
            ['Pipeline Board', route('team.prospects.pipeline'), true],
            ['Follow-Ups', route('team.prospects.follow-ups'), true],
            ['Appointments', route('team.prospects.appointments'), true],
            ['Analytics', route('team.prospects.analytics'), true],
            ['Access Manager', route('team.prospects.access-manager'), auth()->user()?->can('manage prospects')],
            ['AI Coach', route('team.prospects.ai-coach'), true],
            ['Import', route('team.prospects.import'), auth()->user()?->can('import prospects')],
        ] as [$label, $url, $visible])
            @if ($visible)
                <a href="{{ $url }}" class="shrink-0 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:border-[#C8A24A]/50 hover:bg-[#FFF9EA]">
                    {{ $label }}
                </a>
            @endif
        @endforeach
    </div>
</div>
