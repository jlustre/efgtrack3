<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-[#C8A24A]">Administration</p>
                <h1 class="mt-1 text-2xl font-semibold text-[#0B1F3A]">Support operations</h1>
                <p class="mt-1 text-sm text-slate-600">Monitor ticket queues, SLA risk, and enhancement wishlist pipeline.</p>
            </div>
            <a
                href="{{ route('admin.support.wishlist') }}"
                class="rounded-lg border border-[#C8A24A]/50 bg-[#FFF9EA] px-4 py-2 text-sm font-semibold text-[#8A6A1F] transition hover:bg-[#C8A24A]/20"
            >
                Enhancement wishlist board →
            </a>
        </div>

        @if (session('support_admin_status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('support_admin_status') }}
            </div>
        @endif

        <livewire:support.admin-support-dashboard />
    </div>
</x-app-layout>
