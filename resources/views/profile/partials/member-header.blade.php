@php
    $profile = $user->profile;
    $badge = $badge ?? 'Member Profile';
    $showEfgDetails = $showEfgDetails ?? false;
@endphp

<section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
    <div class="bg-[#0B1F3A] px-6 py-8 text-white">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex items-center gap-5">
                <x-user-avatar :user="$user" size="xl" class="border-white/20 bg-white/10" />

                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">{{ $badge }}</p>
                    <h1 class="mt-1 text-3xl font-semibold">{{ $user->name }}</h1>
                    <p class="mt-2 text-sm text-slate-300">{{ $user->email }}</p>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-3 lg:min-w-[32rem]">
                <div class="rounded-md border border-white/10 bg-white/10 p-4">
                    <div class="text-xs uppercase text-slate-300">Current Rank</div>
                    <div class="mt-1 text-lg font-semibold">{{ $user->rank?->code ?? 'Not Set' }}</div>
                </div>
                <div class="rounded-md border border-white/10 bg-white/10 p-4">
                    <div class="text-xs uppercase text-slate-300">Team</div>
                    <div class="mt-1 text-lg font-semibold">{{ $user->team?->name ?? 'Unassigned' }}</div>
                </div>
                <div class="rounded-md border border-white/10 bg-white/10 p-4">
                    <div class="text-xs uppercase text-slate-300">Sponsor</div>
                    <div class="mt-1 text-lg font-semibold">{{ $user->sponsor?->name ?? 'None' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 border-t border-slate-200 bg-slate-50 px-6 py-5 md:grid-cols-4">
        <div>
            <div class="text-xs font-semibold uppercase text-slate-500">Role</div>
            <div class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $user->getRoleNames()->first() ?? 'member' }}</div>
        </div>
        <div>
            <div class="text-xs font-semibold uppercase text-slate-500">Phone</div>
            <div class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $profile?->phone ?? 'Not added' }}</div>
        </div>
        <div>
            <div class="text-xs font-semibold uppercase text-slate-500">Location</div>
            <div class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ collect([$profile?->city, $profile?->province])->filter()->join(', ') ?: 'Not added' }}</div>
        </div>
        <div>
            <div class="text-xs font-semibold uppercase text-slate-500">License</div>
            <div class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $profile?->license_number ?? 'Not added' }}</div>
        </div>
        @if ($showEfgDetails)
            <div>
                <div class="text-xs font-semibold uppercase text-slate-500">EFG Associate ID</div>
                <div class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $profile?->efg_associate_id ?? 'Not added' }}</div>
            </div>
            <div class="md:col-span-3">
                <div class="text-xs font-semibold uppercase text-slate-500">Experior Invite URL</div>
                <div class="mt-1 break-all text-sm font-semibold text-[#0B1F3A]">{{ $profile?->efg_invite_link ?? 'Not added' }}</div>
            </div>
        @endif
    </div>
</section>
