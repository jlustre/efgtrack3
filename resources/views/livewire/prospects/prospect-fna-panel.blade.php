<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm text-slate-600">
                Prospect FNA status:
                <span class="font-semibold text-[#0B1F3A]">{{ $fnaStatuses[$prospect->fna_status] ?? str($prospect->fna_status)->replace('_', ' ')->title() }}</span>
            </p>
        </div>
        @if ($canCreateFna)
            <a href="{{ route('team.fna.create', ['prospect_id' => $prospect->id]) }}" class="rounded-lg bg-[#0B1F3A] px-3 py-1.5 text-xs font-semibold text-white">
                + Create FNA
            </a>
        @endif
        @can('create', \App\Models\FnaClientInvite::class)
            <button type="button" onclick="Livewire.dispatch('open-fna-client-invite-modal', { prospectId: '{{ $prospect->id }}' })" class="rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-3 py-1.5 text-xs font-semibold text-[#0B1F3A]">
                Send FNA Link
            </button>
        @endcan
    </div>

    <div class="space-y-3">
        @forelse ($records as $record)
            <article class="rounded-lg border border-slate-200 bg-white px-4 py-4 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <a href="{{ route('team.fna.show', $record) }}" class="text-sm font-semibold text-[#8A6A1F] hover:underline">
                            {{ $record->reference_code }} — {{ $record->client_name }}
                        </a>
                        <p class="mt-1 text-sm text-slate-600">{{ $record->statusLabel() }} · {{ $record->completeness_score }}% complete</p>
                        @if ($record->calendarEvent)
                            <p class="mt-1 text-xs text-slate-500">
                                Meeting: {{ $record->calendarEvent->starts_at?->format('M j, Y g:i A') }}
                            </p>
                        @endif
                    </div>
                    <div class="text-right text-xs text-slate-500">
                        <p>{{ $record->owner?->name ?? 'Unknown' }}</p>
                        <p>{{ $record->updated_at?->format('M j, Y') }}</p>
                    </div>
                </div>
                @if (in_array($record->status, ['approved_by_cfm', 'scheduled_for_client_review'], true))
                    <a href="{{ route('team.fna.show', $record) }}#schedule" class="mt-3 inline-flex text-xs font-semibold text-[#8A6A1F] hover:underline">
                        Schedule or view client meeting →
                    </a>
                @endif
            </article>
        @empty
            <div class="rounded-lg border border-dashed border-slate-300 bg-white/70 px-6 py-10 text-center">
                <p class="text-sm font-semibold text-[#0B1F3A]">No FNA records linked</p>
                <p class="mt-1 text-sm text-slate-500">Create a Financial Needs Analysis to track discovery, DIME, and CFM review for this prospect.</p>
                @if ($canCreateFna)
                    <a href="{{ route('team.fna.create', ['prospect_id' => $prospect->id]) }}" class="mt-4 inline-flex rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white">
                        Create first FNA
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    @can('create', \App\Models\FnaClientInvite::class)
        <livewire:fna.fna-client-invite-panel :prospect="$prospect" :key="'prospect-fna-invites-'.$prospect->id" />
    @endcan
</div>
