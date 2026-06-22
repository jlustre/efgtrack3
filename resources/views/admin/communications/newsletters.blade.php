<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-4">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-[#C8A24A]">Administration</p>
                <h1 class="mt-1 text-2xl font-semibold text-[#0B1F3A]">Newsletter generator</h1>
                <p class="text-sm text-slate-600">Compile weekly, monthly, or quarterly digests from hub content.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.communications.index') }}" class="rounded-lg border border-[#0B1F3A]/15 px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:border-[#C8A24A]/50 hover:bg-[#FFF9EA]">
                    Analytics dashboard
                </a>
                <a href="{{ route('communications.index') }}" class="rounded-lg border border-[#C8A24A]/50 bg-[#FFF9EA] px-4 py-2 text-sm font-semibold text-[#8A6A1F] transition hover:bg-[#C8A24A]/20">
                    Back to hub
                </a>
            </div>
        </div>

        @if (session('communication_admin_status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('communication_admin_status') }}
            </div>
        @endif

        <livewire:admin.communication.newsletter-generator />
    </div>
</x-app-layout>
