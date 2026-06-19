<x-app-layout>
    <div class="bg-zinc-950 min-h-screen -mx-4 -my-6 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl space-y-4">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-amber-400">Administration</p>
                    <h1 class="mt-1 text-2xl font-semibold text-zinc-100">Support operations</h1>
                </div>
                <a href="{{ route('admin.support.wishlist') }}" class="rounded-lg border border-amber-500/40 bg-amber-500/10 px-4 py-2 text-sm font-semibold text-amber-300 transition duration-200 ease-in-out hover:bg-amber-500/20">
                    Enhancement wishlist board →
                </a>
            </div>

            @if (session('support_admin_status'))
                <div class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">{{ session('support_admin_status') }}</div>
            @endif

            <livewire:support.admin-support-dashboard />
        </div>
    </div>
</x-app-layout>
