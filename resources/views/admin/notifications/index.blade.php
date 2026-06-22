<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-4">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-[#C8A24A]">Administration</p>
                <h1 class="mt-1 text-2xl font-semibold text-[#0B1F3A]">Notification operations</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.notifications.delivery-logs') }}" class="rounded-lg border border-[#0B1F3A]/15 px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:border-[#C8A24A]/50 hover:bg-[#FFF9EA]">
                    Delivery logs
                </a>
                <a href="{{ route('admin.management.index', ['category' => 'notifications']) }}" class="rounded-lg border border-[#C8A24A]/50 bg-[#FFF9EA] px-4 py-2 text-sm font-semibold text-[#8A6A1F] transition hover:bg-[#C8A24A]/20">
                    Configuration tables
                </a>
            </div>
        </div>

        @if (session('notification_admin_status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('notification_admin_status') }}</div>
        @endif

        <livewire:admin.notifications.admin-notification-dashboard />
    </div>
</x-app-layout>
