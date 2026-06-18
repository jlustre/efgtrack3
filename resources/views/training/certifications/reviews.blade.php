<x-app-layout>
    <div class="space-y-6">
        <div class="overflow-visible rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#0B1F3A] via-[#132F55] to-[#0B1F3A] p-6 text-white shadow-lg">
            <a href="{{ route('training.certifications.index') }}" class="text-sm font-semibold text-[#C8A24A] transition hover:text-[#D8B75F]">&larr; Certifications</a>
            <h1 class="mt-2 text-3xl font-semibold">Certification Reviews</h1>
            <p class="mt-2 text-sm text-slate-200">Approve or reject trainee certification requests.</p>
        </div>

        <livewire:training.certification-reviews />
    </div>
</x-app-layout>
