<div class="space-y-6">
    @if (session('fna_invite_status'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('fna_invite_status') }}
        </div>
    @endif

    @if ($createdSecurityCode && $createdInviteUrl)
        <div class="rounded-xl border-2 border-[#C8A24A] bg-[#FFF9EA] p-5">
            <h3 class="text-sm font-bold uppercase tracking-wide text-[#8A6A1F]">Invite created — share these now</h3>
            <p class="mt-2 text-sm text-slate-700">The security code is shown only once. Copy both the link and code for your {{ $recipientContext === 'member' ? 'member' : 'prospect or client' }}.</p>
            <dl class="mt-4 space-y-3 text-sm">
                <div>
                    <dt class="font-semibold text-slate-600">Invite link</dt>
                    <dd class="mt-1 break-all rounded-lg bg-white px-3 py-2 font-mono text-xs text-[#0B1F3A]">{{ $createdInviteUrl }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-slate-600">Security code</dt>
                    <dd class="mt-1 inline-block rounded-lg bg-[#0B1F3A] px-4 py-2 text-2xl font-bold tracking-[0.35em] text-[#C8A24A]">{{ $createdSecurityCode }}</dd>
                </div>
            </dl>
        </div>
    @endif

    @if ($canSend)
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h3 class="text-base font-semibold text-[#0B1F3A]">Send FNA client portal invite</h3>
            @if ($recipientContext === 'member')
                <p class="mt-1 text-sm text-slate-600">This EFGTrack member can complete the FNA through the secure client portal. The portal is separate from their member login.</p>
            @else
                <p class="mt-1 text-sm text-slate-600">Your prospect or member can complete the FNA online. Non-members do not need an EFGTrack account.</p>
            @endif

            <form wire:submit="sendInvite" class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Recipient name</label>
                    <input type="text" wire:model="recipient_name" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                    @error('recipient_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Email</label>
                        <input type="email" wire:model="recipient_email" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                        @error('recipient_email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700">Phone</label>
                        <input type="text" wire:model="recipient_phone" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                        @error('recipient_phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700">Personal message (optional)</label>
                    <textarea wire:model="personal_message" rows="3" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"></textarea>
                </div>
                <button type="submit" class="rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#12345B]">Create invite link</button>
            </form>
        </div>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h3 class="text-base font-semibold text-[#0B1F3A]">Client portal invites</h3>
        @if ($invites->isEmpty())
            <p class="mt-3 text-sm text-slate-600">No client portal invites yet.</p>
        @else
            <div class="mt-4 space-y-3">
                @foreach ($invites as $invite)
                    <article class="rounded-lg border border-slate-200 px-4 py-3 text-sm">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <p class="font-semibold text-[#0B1F3A]">{{ $invite->recipient_name }}</p>
                                <p class="mt-1 text-slate-600">{{ $invite->recipientTypeLabel() }} · {{ $invite->statusLabel() }} · {{ $invite->fnaRecord?->completeness_score ?? 0 }}% complete</p>
                                @if ($invite->last_saved_at)
                                    <p class="mt-1 text-xs text-slate-500">Last saved {{ $invite->last_saved_at->format('M j, Y g:i A') }}</p>
                                @endif
                            </div>
                            @if ($invite->isUsable())
                                <button type="button" wire:click="revokeInvite('{{ $invite->id }}')" wire:confirm="Revoke this invite?" class="text-xs font-semibold text-red-700 hover:underline">Revoke</button>
                            @endif
                        </div>
                        @if ($invite->isUsable())
                            <p class="mt-2 break-all font-mono text-xs text-[#8A6A1F]">{{ $invite->inviteUrl() }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</div>
