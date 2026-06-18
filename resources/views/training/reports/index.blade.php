<x-app-layout>
    <div class="space-y-6">
        <div class="overflow-visible rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#0B1F3A] via-[#132F55] to-[#0B1F3A] p-6 text-white shadow-lg">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <a href="{{ route('training.index') }}" class="text-sm font-semibold text-[#C8A24A] transition hover:text-[#D8B75F]">&larr; Training Center</a>
                    <h1 class="mt-2 text-3xl font-semibold">Training Reports & Analytics</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-200">
                        Track lessons, courses, certifications, and team learning activity across reporting periods.
                    </p>
                </div>
            </div>
        </div>

        @if (session('training_report_status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('training_report_status') }}
            </div>
        @endif

        <livewire:training.training-reports />
    </div>
</x-app-layout>
