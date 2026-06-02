<x-app-layout>
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
            <h1 class="text-2xl font-semibold text-[#0B1F3A]">Add User</h1>
            <p class="mt-2 text-sm text-slate-600">Create a portal user and assign role, rank, team, sponsor, and status.</p>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            @csrf

            @include('admin.users.partials.form', [
                'managedUser' => null,
                'roles' => $roles,
                'ranks' => $ranks,
                'teams' => $teams,
                'sponsors' => $sponsors,
            ])

            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-md bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#12345f]">Create User</button>
            </div>
        </form>
    </div>
</x-app-layout>
