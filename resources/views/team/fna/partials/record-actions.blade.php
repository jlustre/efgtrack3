<a href="{{ route('team.fna.wizard', $fna) }}" class="inline-flex items-center justify-center rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B85F]">Open Wizard</a>
@can('export', $fna)
    <a href="{{ route('team.fna.export', $fna) }}" class="inline-flex items-center justify-center rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Export</a>
@endcan
<a href="{{ route('team.fna.index') }}" class="inline-flex items-center justify-center rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Back to List</a>
