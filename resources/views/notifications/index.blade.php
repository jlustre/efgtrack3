<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Activity Center</p>
                <h1 class="text-2xl font-semibold text-[#0B1F3A]">Notifications</h1>
                <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600">
                    Personal alerts for mentorship, apprenticeship, training, assessments, announcements, events, and rank progress.
                </p>
            </div>

            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">
                    Mark All Read
                </button>
            </form>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            @foreach ([
                ['label' => 'Unread', 'value' => $unreadCount, 'accent' => 'bg-[#C8A24A]'],
                ['label' => 'Read', 'value' => $readCount, 'accent' => 'bg-[#0B1F3A]'],
                ['label' => 'Total', 'value' => $notifications->total(), 'accent' => 'bg-emerald-500'],
                ['label' => 'Types', 'value' => $typeCounts->count(), 'accent' => 'bg-sky-500'],
            ] as $stat)
                <section class="rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $stat['label'] }}</p>
                            <div class="mt-2 text-2xl font-bold text-[#0B1F3A]">{{ $stat['value'] }}</div>
                        </div>
                        <span class="h-3 w-3 rounded-full {{ $stat['accent'] }}"></span>
                    </div>
                </section>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-[1fr_21rem]">
            <section class="overflow-hidden rounded-lg border border-slate-400 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Recent Alerts</h2>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($notifications as $notification)
                        @php
                            $category = data_get($notification->data, 'category', 'General');
                            $tone = match (strtolower($category)) {
                                'mentorship', 'mentor assignment' => 'bg-[#C8A24A]',
                                'training' => 'bg-emerald-500',
                                'event', 'events' => 'bg-sky-500',
                                'rank advancement' => 'bg-purple-500',
                                default => 'bg-[#0B1F3A]',
                            };
                        @endphp

                        <article class="flex gap-4 px-5 py-4 transition hover:bg-slate-50">
                            <div class="mt-1 h-3 w-3 shrink-0 rounded-full {{ $tone }}"></div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <h3 class="text-sm font-semibold text-[#0B1F3A]">{{ data_get($notification->data, 'title', 'Portal notification') }}</h3>
                                    <span class="w-fit rounded-full px-2 py-1 text-xs font-semibold {{ $notification->read() ? 'bg-slate-100 text-slate-500' : 'bg-[#C8A24A]/20 text-[#0B1F3A]' }}">
                                        {{ $notification->read() ? 'Read' : 'Unread' }}
                                    </span>
                                </div>
                                <p class="mt-2 text-sm leading-6 text-slate-600">
                                    {{ data_get($notification->data, 'message', data_get($notification->data, 'body', 'Open notifications to review the latest portal activity.')) }}
                                </p>
                                <div class="mt-3 flex flex-wrap items-center gap-3">
                                    <p class="text-xs font-medium uppercase tracking-wide text-slate-400">{{ $category }} &middot; {{ $notification->created_at->diffForHumans() }}</p>
                                    @unless ($notification->read())
                                        <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}">
                                            @csrf
                                            <button class="rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">
                                                Mark Read
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="px-5 py-10 text-center text-sm text-slate-500">
                            No notifications yet.
                        </div>
                    @endforelse
                </div>

                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $notifications->links() }}
                </div>
            </section>

            <aside class="space-y-6">
                <section class="rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-5 shadow-sm">
                    <h2 class="text-lg font-semibold text-[#0B1F3A]">Notification Types</h2>
                    <div class="mt-4 space-y-3">
                        @forelse ($typeCounts as $type => $count)
                            <div class="flex items-center justify-between rounded-md bg-white px-3 py-2 text-sm">
                                <span class="font-medium text-slate-700">{{ $type }}</span>
                                <span class="rounded-full border border-[#C8A24A] bg-[#FFF4CF] px-2 py-0.5 text-xs font-bold text-[#0B1F3A]">{{ $count }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Notification categories will appear here.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-lg border border-[#C8A24A]/30 bg-[#0B1F3A] p-5 text-white shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-[#C8A24A]">Live Center</p>
                    <h2 class="mt-2 text-lg font-semibold">Database Notifications</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-300">
                        This page now reads real Laravel database notifications and supports read/unread controls.
                    </p>
                </section>
            </aside>
        </div>
    </div>
</x-app-layout>
