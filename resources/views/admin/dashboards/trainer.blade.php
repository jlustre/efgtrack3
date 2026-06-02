<x-app-layout>
    <section class="space-y-6">
        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin Management</p>
            <h1 class="mt-2 text-2xl font-semibold text-[#0B1F3A]">Trainer Workspace</h1>
            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600">
                Training and assessment workspace for modules, lessons, assessments, and learning progress.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <a href="{{ route('admin.training.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Training Manager</div>
                <p class="mt-2 text-sm text-slate-600">Create and organize training modules.</p>
            </a>
            <a href="{{ route('assessments.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Assessments</div>
                <p class="mt-2 text-sm text-slate-600">Review testing and evaluation flow.</p>
            </a>
            <a href="{{ route('resources.index') }}" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm transition hover:border-[#C8A24A]">
                <div class="text-sm font-semibold text-[#0B1F3A]">Training Resources</div>
                <p class="mt-2 text-sm text-slate-600">Manage supporting resource material.</p>
            </a>
        </div>
    </section>
</x-app-layout>
