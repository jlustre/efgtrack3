<x-app-layout>
    <div class="mx-auto max-w-7xl space-y-4">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-[#C8A24A]">Administration</p>
                <h1 class="mt-1 text-2xl font-semibold text-[#0B1F3A]">Communication analytics</h1>
                <p class="text-sm text-slate-600">Engagement metrics, top announcements, and broadcast activity.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('send broadcast')
                    <a href="{{ route('admin.communications.broadcasts') }}" class="rounded-lg border border-[#0B1F3A]/15 px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:border-[#C8A24A]/50 hover:bg-[#FFF9EA]">
                        Broadcast center
                    </a>
                @endcan
                @can('manage newsletters')
                    <a href="{{ route('admin.communications.newsletters') }}" class="rounded-lg border border-[#0B1F3A]/15 px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:border-[#C8A24A]/50 hover:bg-[#FFF9EA]">
                        Newsletters
                    </a>
                @endcan
                <a href="{{ route('admin.communications.acknowledgements') }}" class="rounded-lg border border-[#0B1F3A]/15 px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:border-[#C8A24A]/50 hover:bg-[#FFF9EA]">
                    Acknowledgements
                </a>
                <a href="{{ route('communications.index') }}" class="rounded-lg border border-[#C8A24A]/50 bg-[#FFF9EA] px-4 py-2 text-sm font-semibold text-[#8A6A1F] transition hover:bg-[#C8A24A]/20">
                    Back to hub
                </a>
            </div>
        </div>

        <livewire:admin.communication.admin-communication-dashboard />
    </div>
</x-app-layout>
