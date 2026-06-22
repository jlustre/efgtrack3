<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-4">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-[#C8A24A]">Administration</p>
                <h1 class="mt-1 text-2xl font-semibold text-[#0B1F3A]">Notification delivery logs</h1>
            </div>
            <a href="{{ route('admin.notifications.index') }}" class="rounded-lg border border-[#C8A24A]/50 bg-[#FFF9EA] px-4 py-2 text-sm font-semibold text-[#8A6A1F] transition hover:bg-[#C8A24A]/20">
                Back to dashboard
            </a>
        </div>

        @if (session('notification_admin_status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('notification_admin_status') }}</div>
        @endif

        <livewire:admin.notifications.notification-delivery-logs />
    </div>
</x-app-layout>
