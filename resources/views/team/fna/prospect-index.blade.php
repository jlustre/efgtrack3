<x-app-layout>
    <section class="space-y-6">
        @include('team.fna.partials.page-shell', [
            'title' => 'FNAs — '.$prospect->displayName(),
            'description' => 'Financial Needs Analysis records linked to this prospect.',
            'actions' => '<a href="'.route('team.fna.create').'?prospect_id='.$prospect->id.'" class="inline-flex rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A]">+ Create FNA</a>',
        ])

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @forelse ($records as $record)
                <div class="flex items-center justify-between border-b border-slate-100 py-3 last:border-0">
                    <div>
                        <a href="{{ route('team.fna.show', $record) }}" class="font-semibold text-[#8A6A1F]">{{ $record->reference_code }}</a>
                        <p class="text-sm text-slate-600">{{ $record->statusLabel() }}</p>
                    </div>
                    <span class="text-xs text-slate-500">{{ $record->updated_at?->format('M j, Y') }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-600">No FNA records linked to this prospect yet.</p>
            @endforelse
        </div>
    </section>
</x-app-layout>
