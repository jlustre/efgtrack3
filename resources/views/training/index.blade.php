<x-app-layout>
    <div class="space-y-6">
        <div class="overflow-visible rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#0B1F3A] via-[#132F55] to-[#0B1F3A] p-6 text-white shadow-lg">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">{{ config('training-academy.brand.name') }}</p>
                    <h1 class="mt-2 text-3xl font-semibold">Training Center</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-200">
                        {{ config('training-academy.brand.tagline') }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 pb-1">
                    <a href="{{ route('assessments.index') }}" class="inline-flex items-center rounded-md border border-[#C8A24A]/50 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#C8A24A]/20">
                        Assessments
                    </a>
                    <a href="{{ route('training.assignments.index') }}" class="inline-flex items-center rounded-md border border-[#C8A24A]/50 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#C8A24A]/20">
                        Assignments
                    </a>
                    <a href="{{ route('training.certifications.index') }}" class="inline-flex items-center rounded-md border border-[#C8A24A]/50 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#C8A24A]/20">
                        Certifications
                    </a>
                    <a href="{{ route('training.paths.index') }}" class="inline-flex items-center rounded-md border border-[#C8A24A]/50 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#C8A24A]/20">
                        Learning Paths
                    </a>
                    <a href="{{ route('training.coaching.index') }}" class="inline-flex items-center rounded-md border border-[#C8A24A]/50 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#C8A24A]/20">
                        FAP & Coaching
                    </a>
                    <a href="{{ route('training.sessions.index') }}" class="inline-flex items-center rounded-md border border-[#C8A24A]/50 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#C8A24A]/20">
                        Live Sessions
                    </a>
                    <a href="{{ route('training.achievements.index') }}" class="inline-flex items-center rounded-md border border-[#C8A24A]/50 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#C8A24A]/20">
                        Achievements
                    </a>
                    <a href="{{ route('training.plan.index') }}" class="inline-flex items-center rounded-md border border-[#C8A24A]/50 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#C8A24A]/20">
                        My Learning Plan
                    </a>
                    <a href="{{ route('training.reports.index') }}" class="inline-flex items-center rounded-md border border-[#C8A24A]/50 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#C8A24A]/20">
                        Reports
                    </a>
                    <a href="{{ route('rank-advancement.index') }}" class="inline-flex items-center rounded-md border border-[#C8A24A]/50 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#C8A24A]/20">
                        Rank Advancement
                    </a>
                    @can('manage training')
                        <a href="{{ route('admin.training.index') }}" class="inline-flex items-center rounded-md bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:bg-[#D8B75F]">
                            Manage Training
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        <livewire:training.training-dashboard />
    </div>
</x-app-layout>
