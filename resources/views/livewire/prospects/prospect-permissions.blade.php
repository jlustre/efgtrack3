<div class="space-y-6">
    <div class="overflow-hidden rounded-lg border border-slate-400 bg-gradient-to-br from-white via-slate-50 to-[#FFF9EA] shadow-sm">
        <div class="flex flex-col gap-4 bg-[#0B1F3A] px-6 py-6 text-white md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Prospect Management</p>
                <h1 class="mt-2 text-2xl font-semibold">Access Manager</h1>
                <p class="mt-2 text-sm text-slate-200">Grant, expire, revoke, and audit prospect sharing permissions.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('team.prospects') }}" class="rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold text-white hover:bg-white/10">Dashboard</a>
                <a href="{{ route('team.prospects.shared-by-me') }}" class="rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A]">Shared By Me</a>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="rounded-lg border border-[#C8A24A]/30 bg-white p-4 shadow-sm">
        <h2 class="text-base font-semibold text-[#0B1F3A]">Bulk Visibility Preset</h2>
        <p class="mt-1 text-sm text-slate-600">Apply a visibility preset to one of your prospects.</p>
        <form wire:submit="applyBulkPreset" class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            <label class="block text-sm font-semibold text-[#0B1F3A]">
                Prospect
                <select wire:model="bulkProspectId" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    <option value="">Select prospect…</option>
                    @foreach ($ownedProspects as $prospect)
                        <option value="{{ $prospect->id }}">{{ $prospect->displayName() }} ({{ $visibilityPresets[$prospect->visibility_preset] ?? $prospect->visibility_preset }})</option>
                    @endforeach
                </select>
                @error('bulkProspectId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </label>
            <label class="block text-sm font-semibold text-[#0B1F3A]">
                Preset
                <select wire:model="bulkPreset" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    @foreach ($visibilityPresets as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm font-semibold text-[#0B1F3A]">
                Permission
                <select wire:model="bulkPermissionId" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    <option value="">View Only (default)</option>
                    @foreach ($permissions as $permission)
                        <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm font-semibold text-[#0B1F3A]">
                Expires
                <input type="date" wire:model="bulkExpiresAt" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
            </label>
            <div class="flex items-end">
                <button type="submit" class="w-full rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A]">Apply Preset</button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-lg border border-[#C8A24A]/30 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Shares Granted By You</h2>
            <select wire:model.live="shareStatusFilter" class="rounded-lg border-slate-300 text-sm">
                <option value="active">Active</option>
                <option value="inactive">Expired / Revoked</option>
            </select>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-[#FFF9EA] text-left text-xs font-bold uppercase tracking-wide text-[#0B1F3A]">
                    <tr>
                        <th class="px-4 py-3">Prospect</th>
                        <th class="px-4 py-3">Collaborator</th>
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
                                    <a href="{{ route('team.prospects.records.show', $share->prospect) }}" class="hover:text-[#8A6A1F]">{{ $share->prospect->displayName() }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $share->sharedWith?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $share->permission?->name ?? 'View Only' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $share->granted_at?->format('M j, Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $share->expires_at?->format('M j, Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                @if ($share->isActive())
                                    <button type="button" wire:click="revokeShare({{ $share->id }})" wire:confirm="Revoke this share?" class="text-sm font-semibold text-red-600 hover:text-red-800">Revoke</button>
                                @else
                                    <span class="text-xs font-semibold uppercase text-slate-400">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">No shares match this filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($shares->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">{{ $shares->links() }}</div>
        @endif
    </div>

    <div class="overflow-hidden rounded-lg border border-[#C8A24A]/30 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Access Log</h2>
            <p class="mt-1 text-sm text-slate-600">Activity on prospects you own or shares you granted.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-[#FFF9EA] text-left text-xs font-bold uppercase tracking-wide text-[#0B1F3A]">
                    <tr>
                        <th class="px-4 py-3">When</th>
                        <th class="px-4 py-3">Action</th>
                        <th class="px-4 py-3">Prospect</th>
                        <th class="px-4 py-3">Actor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($accessLogs as $log)
                        <tr>
                            <td class="px-4 py-3 text-slate-600">{{ $log->created_at?->format('M j, Y g:i A') }}</td>
                            <td class="px-4 py-3 font-medium text-[#0B1F3A]">{{ str($log->action)->replace('_', ' ')->title() }}</td>
                            <td class="px-4 py-3 text-slate-600">
                                @if ($log->prospect)
                                    {{ $log->prospect->first_name }} {{ $log->prospect->last_name }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $log->actor?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-500">No access log entries yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($accessLogs->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">{{ $accessLogs->links() }}</div>
        @endif
    </div>
</div>
