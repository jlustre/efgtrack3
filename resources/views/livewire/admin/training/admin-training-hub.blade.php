<div class="space-y-6">
    <div class="rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#0B1F3A] via-[#132F55] to-[#0B1F3A] p-6 text-white shadow-lg">
        <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Admin · Academy Builder</p>
        <h1 class="mt-2 text-3xl font-semibold">Training Content Studio</h1>
        <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-200">
            Build courses, lessons, and learning paths for the EFGTrack Academy. Publish content to the member Training Center.
        </p>
    </div>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-[#F8F3E7] shadow-sm">
        <div class="grid gap-3 p-4 sm:grid-cols-2 xl:grid-cols-5">
            @foreach ([
                ['label' => 'Published courses', 'value' => $stats['courses_published'], 'theme' => 'emerald', 'subtitle' => 'Live in academy'],
                ['label' => 'Draft courses', 'value' => $stats['courses_draft'], 'theme' => 'amber', 'subtitle' => 'Not yet published'],
                ['label' => 'Lessons', 'value' => $stats['lessons'], 'theme' => 'navy', 'subtitle' => 'Across all courses'],
                ['label' => 'Learning paths', 'value' => $stats['paths'], 'theme' => 'gold', 'subtitle' => 'Role-based sequences'],
                ['label' => 'Certifications', 'value' => $stats['certifications'], 'theme' => 'cyan', 'subtitle' => 'Credential programs'],
            ] as $card)
                <x-tracker-stat-card
                    :label="$card['label']"
                    :value="$card['value']"
                    :subtitle="$card['subtitle']"
                    :theme="$card['theme']"
                />
            @endforeach
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
