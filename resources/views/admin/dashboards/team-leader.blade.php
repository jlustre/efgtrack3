<x-app-layout>
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
            <h1 class="mt-2 text-2xl font-semibold text-[#0B1F3A]">Team Leader Workspace</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
                Team visibility for onboarding, licensing, rank advancement, mentor assignment, and apprentice progress.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <a href="{{ route('team.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">My Team</div>
                <p class="mt-2 text-sm text-slate-600">View team hierarchy and member progress.</p>
            </a>
            <a href="{{ route('rank-advancement.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Rank Advancement</div>
                <p class="mt-2 text-sm text-slate-600">Review rank progress and bottlenecks.</p>
            </a>
            <a href="{{ route('apprenticeship.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Apprenticeship</div>
                <p class="mt-2 text-sm text-slate-600">Track CFM assignments and apprentice status.</p>
            </a>
        </div>
    </section>
</x-app-layout>
