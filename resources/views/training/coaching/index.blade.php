<x-app-layout>
    <div class="space-y-6">
        <div class="overflow-visible rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#0B1F3A] via-[#132F55] to-[#0B1F3A] p-6 text-white shadow-lg">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <a href="{{ route('training.index') }}" class="text-sm font-semibold text-[#C8A24A] transition hover:text-[#D8B75F]">&larr; Training Center</a>
                    <h1 class="mt-2 text-3xl font-semibold">FAP & Coaching Center</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-200">
                        Field Apprenticeship progress, mentor coaching reviews, live sessions, and CFM sign-off.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('apprenticeship.index') }}" class="inline-flex rounded-md border border-[#C8A24A]/50 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#C8A24A]/20">
                        FAP Checklist
                    </a>
                    @if ($hub['is_mentor'])
                        <a href="{{ route('cfm.portal') }}" class="inline-flex rounded-md border border-[#C8A24A]/50 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#C8A24A]/20">
                            CFM Portal
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <livewire:training.coaching-center />
    </div>
</x-app-layout>
