@if ($featured->isNotEmpty() || $unreadCount > 0)
    <div class="space-y-3">
        @if ($unreadCount > 0)
            <a
                href="{{ route('communications.index', ['unreadOnly' => 1]) }}"
                class="flex items-center justify-between rounded-lg border border-[#C8A24A]/30 bg-[#FFF9EA] px-3 py-2 text-sm transition hover:border-[#C8A24A]"
            >
                <span class="font-semibold text-[#0B1F3A]">{{ $unreadCount }} unread {{ Str::plural('announcement', $unreadCount) }}</span>
                <span class="text-xs font-semibold text-[#8A6A1F]">View →</span>
            </a>
        @endif

        @foreach ($featured as $item)
            <a
                href="{{ route('communications.show', $item['slug']) }}"
                class="block rounded-lg border border-[#0B1F3A]/10 bg-gradient-to-r from-[#0B1F3A] to-[#132a4d] px-3 py-3 text-white transition hover:border-[#C8A24A]/40"
            >
                @if (! empty($item['category']))
                    <span class="text-[0.65rem] font-semibold uppercase tracking-wide text-[#C8A24A]">{{ $item['category'] }}</span>
                @endif
                <div class="mt-1 text-sm font-semibold">{{ $item['title'] }}</div>
                @if (! empty($item['summary']))
                    <p class="mt-1 line-clamp-2 text-xs text-slate-300">{{ $item['summary'] }}</p>
                @endif
            </a>
        @endforeach
    </div>
@endif
