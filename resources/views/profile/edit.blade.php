<x-app-layout>
    @php
        $profile = $user->profile;
        $latestInvitationUrl = session('invitation_url');
        $newInvitation = $recentInvitations->firstWhere('id', session('invitation_id'));
        $newInvitationEmail = $newInvitation ? $invitationEmails->get($newInvitation->id) : null;
    @endphp

    <div class="space-y-6">
        <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="bg-[#0B1F3A] px-6 py-8 text-white">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="flex items-center gap-5">
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full border border-[#C8A24A]/50 bg-white/10 text-2xl font-bold">
                            {{ str($user->name)->substr(0, 1)->upper() }}
                        </div>

                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Member Profile</p>
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
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
                @include('profile.partials.update-profile-information-form')
            </section>

            <aside class="space-y-6">
                <section class="rounded-lg border border-[#C8A24A]/40 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Invite A Member</p>
                            <h2 class="mt-1 text-xl font-semibold text-[#0B1F3A]">Registration Link</h2>
                        </div>
                        <span class="rounded-full bg-[#0B1F3A] px-3 py-1 text-xs font-semibold text-white">Sponsor</span>
                    </div>

                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        Create a single-use invitation link. The new member will be registered under you as their sponsor.
                    </p>

                    <form method="POST" action="{{ route('profile.invitations.store') }}" class="mt-5 space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="invite_email" :value="__('Invitee Email Optional')" />
                            <x-text-input id="invite_email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" placeholder="new.member@example.com" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <x-primary-button>{{ __('Generate Invite Link') }}</x-primary-button>
                    </form>

                    @if ($latestInvitationUrl)
                        <div x-data="{ copied: false, mailOpen: false, link: @js($latestInvitationUrl) }" class="mt-5 rounded-md border border-[#C8A24A]/40 bg-[#C8A24A]/10 p-4">
                            <div class="text-sm font-semibold text-[#0B1F3A]">New invitation link</div>
                            <div class="mt-3 break-all rounded-md bg-white p-3 text-sm text-slate-700">{{ $latestInvitationUrl }}</div>

                            <div class="mt-3 flex flex-wrap gap-2">
                                <button type="button" class="rounded-md bg-[#0B1F3A] px-3 py-2 text-sm font-semibold text-white hover:bg-[#132F55]" x-on:click="navigator.clipboard.writeText(link); copied = true; setTimeout(() => copied = false, 2000)">
                                    <span x-show="! copied">Copy Link</span>
                                    <span x-show="copied">Copied</span>
                                </button>

                                @if ($newInvitation && $newInvitationEmail)
                                    <button type="button" class="rounded-md bg-[#C8A24A] px-3 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D5B765]" x-on:click="mailOpen = true">
                                        Mail Link
                                    </button>
                                @endif
                            </div>

                            @if ($newInvitation && $newInvitationEmail)
                                @include('profile.partials.invitation-email-modal', [
                                    'invitation' => $newInvitation,
                                    'emailPreview' => $newInvitationEmail,
                                    'modalState' => 'mailOpen',
                                    'inputPrefix' => 'new',
                                ])
                            @endif
                        </div>
                    @endif

                    @if (session('status') === 'invitation-email-sent')
                        <div class="mt-5 rounded-md border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-700">
                            Invitation email sent.
                        </div>
                    @endif

                    @if (session('status') === 'invitation-deleted')
                        <div class="mt-5 rounded-md border border-slate-200 bg-slate-50 p-4 text-sm font-semibold text-slate-700">
                            Invitation link deleted. You can create a new link for that email if they have not registered yet.
                        </div>
                    @endif
                </section>

                <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Recent Invitations</h2>

                    <div class="mt-4 space-y-3">
                        @forelse ($recentInvitations as $invitation)
                            @php($emailPreview = $invitationEmails->get($invitation->id))

                            <div x-data="{ mailOpen: false }" class="rounded-md border border-slate-100 bg-slate-50 p-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-sm font-semibold text-[#0B1F3A]">{{ $invitation->code }}</div>
                                    <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $invitation->isAvailable() ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">
                                        {{ $invitation->isAvailable() ? 'Active' : 'Used/Closed' }}
                                    </span>
                                </div>
                                <div class="mt-1 text-xs text-slate-500">
                                    {{ $invitation->email ?? 'Open email' }} &middot; {{ $invitation->uses_count }}/{{ $invitation->max_uses }} used
                                    @if ($invitation->last_emailed_at)
                                        &middot; mailed {{ $invitation->last_emailed_at->diffForHumans() }}
                                    @endif
                                </div>
                                <div class="mt-2 break-all text-xs text-slate-500">{{ $invitation->invitationUrl() }}</div>

                                @if ($invitation->isAvailable() && $emailPreview)
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <button type="button" class="rounded-md bg-[#0B1F3A] px-3 py-2 text-xs font-semibold text-white hover:bg-[#132F55]" x-on:click="mailOpen = true">
                                            Mail Link
                                        </button>

                                        <form method="POST" action="{{ route('profile.invitations.destroy', $invitation) }}" onsubmit="return confirm('Delete this invitation link? This will deactivate it and allow a new invite for this email if they have not registered yet.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                Delete
                                            </button>
                                        </form>
                                    </div>

                                    @include('profile.partials.invitation-email-modal', [
                                        'invitation' => $invitation,
                                        'emailPreview' => $emailPreview,
                                        'modalState' => 'mailOpen',
                                        'inputPrefix' => 'invitation_'.$invitation->id,
                                    ])
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-slate-600">No invitations created yet.</p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                @include('profile.partials.update-password-form')
            </section>
        </div>
    </div>
</x-app-layout>
