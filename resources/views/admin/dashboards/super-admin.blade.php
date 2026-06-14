<x-app-layout>
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
            <h1 class="mt-2 text-2xl font-semibold text-[#0B1F3A]">Super Admin Control Center</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
                Full platform oversight for users, roles, ranks, teams, training, CFM certification, and system settings.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <a href="{{ route('admin.management.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Admin Management</div>
                <p class="mt-2 text-sm text-slate-600">CRUD access for ranks, teams, onboarding, licensing, content, events, badges, and templates.</p>
            </a>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">User Management</div>
                <p class="mt-2 text-sm text-slate-600">Change roles, rank, team, status, and sponsorship details.</p>
            </a>
            <a href="{{ route('admin.roles.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Roles & Permissions</div>
                <p class="mt-2 text-sm text-slate-600">Manage role-based access across the portal.</p>
            </a>
            <a href="{{ route('admin.ranks.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Ranks & Teams</div>
                <p class="mt-2 text-sm text-slate-600">Adjust FA defaults, rank paths, and team placement.</p>
            </a>
            <a href="{{ route('admin.management.resource.index', 'email-templates') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Email Templates</div>
                <p class="mt-2 text-sm text-slate-600">Edit transactional email copy, subjects, and activation status.</p>
            </a>
        </div>
    </section>
</x-app-layout>
