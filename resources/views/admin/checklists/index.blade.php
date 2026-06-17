<x-app-layout>
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
            <h1 class="mt-2 text-2xl font-semibold text-[#0B1F3A]">Checklists</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
                Unified checklist administration for onboarding, licensing, FAP, CFM training, mentoring, and future checklist types.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <a href="{{ route('admin.management.resource.index', 'checklist-types') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Checklist Types</div>
                <p class="mt-2 text-sm leading-6 text-slate-600">Define checklist categories such as onboarding, FAP, CFM training, mentoring, and licensing.</p>
            </a>

            <a href="{{ route('admin.management.resource.index', 'checklist-instructions') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Checklist Instructions</div>
                <p class="mt-2 text-sm leading-6 text-slate-600">Rich-text help content, manual links, and reference URLs shown while members complete each checklist.</p>
            </a>

            <a href="{{ route('admin.management.resource.index', 'checklists') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Checklist Items</div>
                <p class="mt-2 text-sm leading-6 text-slate-600">Manage all checklist steps and items across onboarding, licensing, FAP, CFM training, and CFM mentoring.</p>
            </a>
        </div>
    </section>
</x-app-layout>
