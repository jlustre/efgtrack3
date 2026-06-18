<div class="space-y-6">
    <div class="rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#0B1F3A] via-[#132F55] to-[#0B1F3A] p-6 text-white shadow-lg">
        <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin · Academy Builder</p>
        <h1 class="mt-2 text-3xl font-semibold">Training Content Studio</h1>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-200">
            Build courses, lessons, and learning paths for the EFGTrack Academy. Publish content to the member Training Center.
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[0.68rem] font-semibold uppercase tracking-wide text-slate-500">Published courses</p>
            <p class="mt-2 text-3xl font-bold text-emerald-700">{{ $stats['courses_published'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[0.68rem] font-semibold uppercase tracking-wide text-slate-500">Draft courses</p>
            <p class="mt-2 text-3xl font-bold text-amber-700">{{ $stats['courses_draft'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[0.68rem] font-semibold uppercase tracking-wide text-slate-500">Lessons</p>
            <p class="mt-2 text-3xl font-bold text-[#0B1F3A]">{{ $stats['lessons'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[0.68rem] font-semibold uppercase tracking-wide text-slate-500">Learning paths</p>
            <p class="mt-2 text-3xl font-bold text-[#8A6A1F]">{{ $stats['paths'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[0.68rem] font-semibold uppercase tracking-wide text-slate-500">Certifications</p>
            <p class="mt-2 text-3xl font-bold text-sky-700">{{ $stats['certifications'] }}</p>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <a href="{{ route('admin.training.courses.index') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#C8A24A]/40 hover:bg-[#FFF9EA]">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Course Builder</h2>
            <p class="mt-2 text-sm text-slate-600">Create and edit academy courses with lessons, publishing rules, and academy metadata.</p>
        </a>
        <a href="{{ route('admin.training.paths.index') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#C8A24A]/40 hover:bg-[#FFF9EA]">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Learning Path Builder</h2>
            <p class="mt-2 text-sm text-slate-600">Assemble role-based paths from published courses with required sequencing.</p>
        </a>
        <a href="{{ route('training.assignments.manage') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#C8A24A]/40 hover:bg-[#FFF9EA]">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Assign Training</h2>
            <p class="mt-2 text-sm text-slate-600">Assign courses to members with due dates and track completion.</p>
        </a>
        <a href="{{ route('admin.management.resource.index', 'training-categories') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#C8A24A]/40 hover:bg-[#FFF9EA]">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Categories</h2>
            <p class="mt-2 text-sm text-slate-600">Manage training category taxonomy via Admin Management tables.</p>
        </a>
        <a href="{{ route('admin.management.resource.index', 'assessments') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#C8A24A]/40 hover:bg-[#FFF9EA]">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Assessments & Questions</h2>
            <p class="mt-2 text-sm text-slate-600">Build assessments, questions, and answers linked to courses.</p>
        </a>
        <a href="{{ route('training.index') }}" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-[#C8A24A]/40 hover:bg-[#FFF9EA]">
            <h2 class="text-lg font-semibold text-[#0B1F3A]">Preview Training Center</h2>
            <p class="mt-2 text-sm text-slate-600">Open the member-facing academy to verify published content.</p>
        </a>
    </div>
</div>
