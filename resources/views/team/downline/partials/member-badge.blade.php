@php
    $rankCode = is_array($member)
        ? ($member['rank'] ?? 'FA')
        : ($member->rank?->code ?? 'FA');
@endphp

<span class="inline-flex items-center rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2.5 py-1 text-xs font-bold text-[#0B1F3A]">
    {{ $rankCode }}
</span>
