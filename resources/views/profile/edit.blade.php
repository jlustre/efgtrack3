<x-app-layout>
    @php
        $isOwnProfile = $isOwnProfile ?? true;
        $profile = $user->profile;
        $latestInvitationUrl = session('invitation_url');
        $newInvitation = $isOwnProfile ? $invitationHistory->firstWhere('id', session('invitation_id')) : null;
        $newInvitationEmail = $newInvitation ? $invitationEmails->get($newInvitation->id) : null;
        $efgDetailsFeedback = session('efg_details_feedback');
    @endphp

    <div
        @if ($isOwnProfile)
            x-data="{
                historyOpen: @js(
                    request()->boolean('open_invitations')
                    || in_array(session('status'), ['invitation-created', 'invitation-deleted', 'invitation-email-sent'], true)
                )
            }"
            x-effect="document.documentElement.classList.toggle('overflow-hidden', historyOpen)"
        @endif
    >
    <div class="space-y-6">
        @if (! $isOwnProfile)
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white px-4 py-3 shadow-sm">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Team Member Profile</p>
                    <p class="mt-1 text-sm text-slate-600">Viewing {{ $user->name }}&rsquo;s profile and progress tabs.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    @can('create', \App\Models\FnaClientInvite::class)
                        @if (auth()->user()->profile?->license_number)
                            <button type="button" onclick="Livewire.dispatch('open-fna-client-invite-member-modal', { memberUserId: {{ $user->id }} })" class="inline-flex items-center rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B85F]">
                                Send FNA Link
                            </button>
                        @endif
                    @endcan
                    <a href="{{ route('team.index') }}" class="inline-flex items-center rounded-lg border border-[#C8A24A] bg-[#FFF9EA] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#F7E8B8]">
                        Back to Team Command Center
                    </a>
                </div>
            </div>
        @endif

        @include('profile.partials.member-header', [
            'user' => $user,
            'badge' => $isOwnProfile ? 'Member Profile' : 'Team Member Profile',
            'showEfgDetails' => $isOwnProfile,
        ])

        <div class="grid gap-6 {{ $isOwnProfile ? 'xl:grid-cols-3' : '' }}">
            <div class="{{ $isOwnProfile ? 'xl:col-span-2' : '' }}">
                @include('profile.partials.member-tabs')
            </div>

            @if ($isOwnProfile)
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

                    <div class="mt-4 flex flex-wrap gap-2">
                        <button
                            type="button"
                            class="rounded-md border border-[#C8A24A] bg-[#FFF9EA] px-3 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#F7E8B8]"
                            x-on:click="historyOpen = true"
                        >
                            View Invitation Log
                            @if ($invitationHistory->isNotEmpty())
                                <span class="ml-1 rounded-full bg-[#0B1F3A] px-2 py-0.5 text-xs text-[#C8A24A]">{{ $invitationHistory->count() }}</span>
                            @endif
                        </button>
                    </div>

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
                            Invitation email sent. Open the invitation log for details.
                        </div>
                    @endif

                    @if (session('status') === 'invitation-deleted')
                        <div class="mt-5 rounded-md border border-slate-200 bg-slate-50 p-4 text-sm font-semibold text-slate-700">
                            Invitation link deleted. You can create a new link for that email if they have not registered yet.
                        </div>
                    @endif

                </section>

                @include('profile.partials.efg-details-panel', [
                    'user' => $user,
                    'profile' => $profile,
                    'efgDetailsFeedback' => $efgDetailsFeedback,
                ])

                @include('profile.partials.profile-completion-panel', [
                    'profileCompletion' => $profileCompletion,
                    'isOwnProfile' => $isOwnProfile,
                ])
            </aside>
            @endif
        </div>
    </div>

        @if ($isOwnProfile)
            @include('profile.partials.invitation-history-modal', [
                'invitationHistory' => $invitationHistory,
                'invitationEmails' => $invitationEmails,
            ])
        @endif
    </div>

    @if ($isOwnProfile)
        @include('partials.rich-text-editor-scripts')
    @else
        @can('create', \App\Models\FnaClientInvite::class)
            <livewire:fna.fna-client-invite-modal />
        @endcan
    @endif
</x-app-layout>
