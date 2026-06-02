<x-app-layout>
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
            <h1 class="mt-2 text-2xl font-semibold text-[#0B1F3A]">Certified Field Mentor Workspace</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
                Mentor-focused workspace for assigned apprentices, notes, sessions, and Field Apprenticeship Program progress.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <a href="{{ route('apprenticeship.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Apprentice Roster</div>
                <p class="mt-2 text-sm text-slate-600">View apprentices assigned to you.</p>
            </a>
            <a href="{{ route('training.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Mentor Training</div>
                <p class="mt-2 text-sm text-slate-600">Review training connected to apprenticeship.</p>
            </a>
            <a href="{{ route('resources.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Mentor Resources</div>
                <p class="mt-2 text-sm text-slate-600">Access field support materials.</p>
            </a>
        </div>
    </section>
</x-app-layout>
