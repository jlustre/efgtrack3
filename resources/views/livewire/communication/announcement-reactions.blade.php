<div>
    <div class="flex flex-wrap items-center gap-2">
        @foreach ($reactions as $code => $meta)
            @php
                $count = $counts[$code] ?? 0;
                $active = $userReaction === $code;
            @endphp
            <button
                type="button"
                wire:click="react('{{ $code }}')"
                @class([
                    'inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-sm font-semibold transition',
                    'border-[#C8A24A] bg-[#FFF9EA] text-[#8A6A1F]' => $active,
                    'border-slate-200 bg-white text-slate-700 hover:border-[#C8A24A]/50 hover:bg-[#FFF9EA]' => ! $active,
                ])
            >
                <span aria-hidden="true">{{ $meta['icon'] }}</span>
                <span>{{ $meta['label'] }}</span>
                @if ($count > 0)
                    <span @class(['text-xs', 'text-[#8A6A1F]' => $active, 'text-slate-500' => ! $active])>{{ $count }}</span>
                @endif
            </button>
        @endforeach
    </div>
</div>
