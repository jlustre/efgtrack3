<x-app-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
                <h1 class="text-2xl font-semibold text-[#0B1F3A]">Manage User</h1>
                <p class="mt-2 text-sm text-slate-600">{{ $managedUser->name }} &middot; {{ $managedUser->email }}</p>
            </div>

            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Back To Users
            </a>
        </div>

        @if (session('status'))
            <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
                {{ str(session('status'))->replace('-', ' ')->title() }}
            </div>
        @endif

        @if ($errors->has('user'))
            <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
                {{ $errors->first('user') }}
            </div>
        @endif

        <section class="grid gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Role</div>
                <div class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $managedUser->getRoleNames()->first() ?? 'none' }}</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Rank</div>
                <div class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $managedUser->rank?->code ?? '-' }}</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Team</div>
                <div class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $managedUser->team?->name ?? '-' }}</div>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">Last Login</div>
                <div class="mt-1 text-sm font-semibold text-[#0B1F3A]">{{ $managedUser->last_login_at?->diffForHumans() ?? 'Never' }}</div>
            </div>
        </section>

        @if ($managedUser->trashed())
            <section class="rounded-lg border border-amber-200 bg-amber-50 p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-amber-800">Archived User</h2>
                <p class="mt-2 text-sm text-amber-700">This user is soft deleted. Restore the account before making changes.</p>

                <form method="POST" action="{{ route('admin.users.restore', $managedUser->id) }}" class="mt-4">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700">Restore User</button>
                </form>
            </section>
        @else
            <form method="POST" action="{{ route('admin.users.update', $managedUser) }}" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                @method('PATCH')

                @include('admin.users.partials.form', [
                    'managedUser' => $managedUser,
                    'roles' => $roles,
                    'ranks' => $ranks,
                    'teams' => $teams,
                    'sponsors' => $sponsors,
                ])

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="submit" class="rounded-md bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#12345f]">Save Changes</button>
                </div>
            </form>

            <section class="rounded-lg border border-amber-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-[#0B1F3A]">Messaging Access</h2>
                <p class="mt-2 text-sm text-slate-600">Suspend a member from sending messages when messaging is used for non-business or inappropriate topics.</p>

                @if ($managedUser->isMessagingSuspended())
                    <div class="mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <p class="font-semibold">Messaging suspended since {{ $managedUser->messaging_suspended_at?->format('M j, Y g:i A') }}</p>
                        @if ($managedUser->messaging_suspension_reason)
                            <p class="mt-2"><span class="font-semibold">Reason:</span> {{ $managedUser->messaging_suspension_reason }}</p>
                        @endif
                        @if ($managedUser->messagingSuspendedBy)
                            <p class="mt-1 text-xs text-red-700">Suspended by {{ $managedUser->messagingSuspendedBy->name }}</p>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('admin.users.messaging.restore', $managedUser) }}" class="mt-4" data-no-page-loader>
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Restore Messaging Access</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.messaging.suspend', $managedUser) }}" class="mt-4 space-y-4" data-no-page-loader onsubmit="return confirm('Suspend this member from messaging?');">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="messaging_suspension_reason" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Suspension reason</label>
                            <textarea
                                id="messaging_suspension_reason"
                                name="messaging_suspension_reason"
                                rows="3"
                                required
                                class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
                                placeholder="Example: Repeated personal/off-topic messages after prior warning."
                            >{{ old('messaging_suspension_reason') }}</textarea>
                            @error('messaging_suspension_reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('messaging')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">Suspend Messaging Access</button>
                    </form>
                @endif
            </section>

            <section class="rounded-lg border border-red-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-red-700">Archive User</h2>
                <p class="mt-2 text-sm text-slate-600">Soft delete this user and deactivate their account. Related profile, invitation, and mentor assignment records are preserved as archived records.</p>

                <form method="POST" action="{{ route('admin.users.destroy', $managedUser) }}" class="mt-4" onsubmit="return confirm('Archive this user?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">Archive User</button>
                </form>
            </section>
        @endif
    </div>
</x-app-layout>
