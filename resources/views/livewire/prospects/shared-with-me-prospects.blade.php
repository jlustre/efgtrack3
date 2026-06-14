<div class="space-y-6">
    <div class="overflow-hidden rounded-lg border border-slate-400 bg-gradient-to-br from-white via-slate-50 to-[#FFF9EA] shadow-sm">
        <div class="flex flex-col gap-4 bg-[#0B1F3A] px-6 py-6 text-white md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Prospect Management</p>
                <h1 class="mt-2 text-2xl font-semibold">Shared With Me</h1>
                <p class="mt-2 text-sm text-slate-200">Prospect records collaborators have shared with you.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('team.prospects') }}" class="rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Dashboard</a>
                <a href="{{ route('team.prospects.shared-by-me') }}" class="rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A]">Shared By Me</a>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg border border-[#C8A24A]/30 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-[#FFF9EA] text-left text-xs font-bold uppercase tracking-wide text-[#0B1F3A]">
                    <tr>
                        <th class="px-4 py-3">Prospect</th>
                        <th class="px-4 py-3">Shared By</th>
                        <th class="px-4 py-3">Stage</th>
                        <th class="px-4 py-3">Permission</th>
                        <th class="px-4 py-3">Granted</th>
                        <th class="px-4 py-3">Expires</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($shares as $share)
                        <tr>
                            <td class="px-4 py-3 font-medium text-[#0B1F3A]">
                                @if ($share->prospect)
                                    {{ $share->prospect->displayName() }}
                                    <span class="ml-1 text-xs font-semibold uppercase text-slate-500">{{ $share->prospect->interest_level }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $share->grantedBy?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $share->prospect?->stage?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $share->permission?->name ?? 'View Only' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $share->granted_at?->format('M j, Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $share->expires_at?->format('M j, Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                @if ($share->prospect && auth()->user()->can('view', $share->prospect))
                                    <a href="{{ route('team.prospects.records.show', $share->prospect) }}" class="text-sm font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">View Profile</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">No prospects have been shared with you.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($shares->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">{{ $shares->links() }}</div>
        @endif
    </div>
</div>
