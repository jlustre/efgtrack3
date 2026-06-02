<x-app-layout>
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
            <h1 class="mt-2 text-2xl font-semibold text-[#0B1F3A]">Checklists</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
                Checklist administration and review for onboarding, licensing, apprenticeship, CFM training, and future task templates.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <a href="{{ route('admin.management.resource.index', 'onboarding-steps') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Onboarding Checklist</div>
                <p class="mt-2 text-sm leading-6 text-slate-600">View or manage steps new members complete during onboarding.</p>
            </a>

            <a href="{{ route('admin.management.resource.index', 'licensing-steps') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Licensing Checklist</div>
                <p class="mt-2 text-sm leading-6 text-slate-600">View or manage licensing milestones and required actions.</p>
            </a>

            <a href="{{ route('admin.management.resource.index', 'apprenticeship-steps') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Apprenticeship Checklist</div>
                <p class="mt-2 text-sm leading-6 text-slate-600">View or manage Field Apprenticeship Program steps.</p>
            </a>

            <a href="{{ route('admin.management.resource.index', 'cfm-training-modules') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">CFM Training Checklist</div>
                <p class="mt-2 text-sm leading-6 text-slate-600">View or manage CFM certification training requirements.</p>
            </a>
        </div>
    </section>
</x-app-layout>
