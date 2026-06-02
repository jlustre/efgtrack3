<x-app-layout>
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
            <h1 class="mt-2 text-2xl font-semibold text-[#0B1F3A]">Agency Owner Dashboard</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
                Agency-level controls for users, ranks, teams, licensing progress, training, mentorship, and announcements.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Agency Users</div>
                <p class="mt-2 text-sm text-slate-600">Change member role, rank, and team assignment.</p>
            </a>
            <a href="{{ route('admin.training.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Training Operations</div>
                <p class="mt-2 text-sm text-slate-600">Review training readiness and publishing queues.</p>
            </a>
            <a href="{{ route('admin.cfm.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">CFM Certification</div>
                <p class="mt-2 text-sm text-slate-600">Monitor mentor readiness and approval workflow.</p>
            </a>
        </div>
    </section>
</x-app-layout>
