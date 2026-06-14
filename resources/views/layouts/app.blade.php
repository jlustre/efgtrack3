<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EFGTrack') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-[#F5F7FA] font-sans text-[#172033] antialiased">
        <div
            x-data="{
                sidebarOpen: false,
                sidebarCollapsed: false,
                isDesktop: window.matchMedia('(min-width: 1024px)').matches,
                updateViewport() {
                    this.isDesktop = window.matchMedia('(min-width: 1024px)').matches;

                    if (this.isDesktop) {
                        this.sidebarOpen = false;
                    }
                },
            }"
            x-init="updateViewport(); window.addEventListener('resize', () => updateViewport())"
            class="min-h-screen lg:flex"
        >
            <div
                x-show="sidebarOpen && ! isDesktop"
                x-transition.opacity
                x-on:click="sidebarOpen = false"
                class="fixed inset-0 z-30 bg-slate-900/50"
                style="display: none;"
            ></div>

            <aside
                class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full overflow-hidden bg-[#0B1F3A] text-white transition-all duration-300 lg:static lg:translate-x-0"
                x-bind:style="sidebarCollapsed && isDesktop ? 'width: 0px;' : 'width: 18rem;'"
                :class="{ 'translate-x-0': sidebarOpen }"
            >
                <div class="flex h-16 items-center border-b border-white/10 px-6">
                    <a href="{{ Route::has('dashboard') ? route('dashboard') : url('/') }}">
                        <div class="text-lg font-semibold tracking-wide">EFGTrack</div>
                        <div class="text-xs uppercase text-[#C8A24A]">Experior Team Portal</div>
                    </a>
                </div>

                @include('layouts.navigation')
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-30 border-b border-slate-200 bg-white px-4 py-3 shadow-sm lg:px-8">
                    @php
                        $user = auth()->user();
                        $topbarUnreadNotificationCount = $user?->unreadNotifications()->count() ?? 0;
                        $topbarNotifications = $user?->notifications()->latest()->limit(3)->get() ?? collect();
                        $openTaskCount = $user ? app(\App\Http\Controllers\TaskController::class)->openTaskCountFor($user) : 0;
                        $user?->loadMissing(['profile', 'rank']);
                    @endphp

                    <div class="flex flex-wrap items-center gap-3">
                        <button
                            type="button"
                            class="rounded-md p-2 text-[#0B1F3A] hover:bg-slate-100"
                            x-on:click="isDesktop ? (sidebarCollapsed = ! sidebarCollapsed) : (sidebarOpen = ! sidebarOpen)"
                            :aria-expanded="isDesktop ? ! sidebarCollapsed : sidebarOpen"
                        >
                            <span class="sr-only">Toggle navigation</span>
                            <span class="block h-0.5 w-5 bg-current"></span>
                            <span class="mt-1 block h-0.5 w-5 bg-current"></span>
                            <span class="mt-1 block h-0.5 w-5 bg-current"></span>
                        </button>

                        <form method="GET" action="{{ route('search.index') }}" class="order-3 w-full sm:order-none sm:min-w-72 sm:max-w-md sm:flex-1 lg:max-w-xl">
                            <label for="topbar-search" class="sr-only">Search EFGTrack</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <circle cx="11" cy="11" r="7"></circle>
                                        <path d="m20 20-3.5-3.5"></path>
                                    </svg>
                                </span>
                                <input
                                    id="topbar-search"
                                    name="q"
                                    value="{{ request('q') }}"
                                    type="search"
                                    placeholder="Search members, training, resources..."
                                    class="h-10 w-full rounded-full border-slate-200 bg-slate-50 pl-10 pr-4 text-sm shadow-sm transition focus:border-[#C8A24A] focus:bg-white focus:ring-[#C8A24A]"
                                >
                            </div>
                        </form>

                        <div class="ml-auto flex items-center gap-2 sm:gap-3">
                            @auth
                                <a
                                    href="{{ route('tasks.index') }}"
                                    class="relative flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:border-[#C8A24A] hover:text-[#0B1F3A]"
                                    title="{{ $openTaskCount > 0 ? $openTaskCount.' open tasks' : 'My Tasks' }}"
                                >
                                    <span class="sr-only">Open my tasks</span>
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M9 11l3 3L22 4"></path>
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                    </svg>
                                    @if ($openTaskCount > 0)
                                    <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-[#0B1F3A] px-1 text-[0.65rem] font-bold text-[#C8A24A]">
                                        {{ $openTaskCount }}
                                    </span>
                                    @endif
                                </a>

                                <x-dropdown align="right" width="80" contentClasses="bg-white p-0">
                                    <x-slot name="trigger">
                                        <button type="button" title="{{ $topbarUnreadNotificationCount > 0 ? $topbarUnreadNotificationCount.' unread notifications' : 'Notifications' }}" class="relative flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:border-[#C8A24A] hover:text-[#0B1F3A]">
                                            <span class="sr-only">Open notifications</span>
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                            </svg>
                                            @if ($topbarUnreadNotificationCount > 0)
                                            <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-[#C8A24A] px-1 text-[0.65rem] font-bold text-[#0B1F3A]">
                                                {{ $topbarUnreadNotificationCount }}
                                            </span>
                                            @endif
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <div class="w-80 max-w-[calc(100vw-2rem)]">
                                            <div class="border-b border-slate-100 px-4 py-3">
                                                <div class="text-sm font-semibold text-[#0B1F3A]">Notifications</div>
                                                <div class="text-xs text-slate-500">{{ $topbarUnreadNotificationCount }} unread update{{ $topbarUnreadNotificationCount === 1 ? '' : 's' }}</div>
                                            </div>

                                            <div class="max-h-72 overflow-y-auto py-1">
                                                @forelse ($topbarNotifications as $notification)
                                                    <div class="flex gap-2 px-4 py-3 transition hover:bg-slate-50">
                                                        <a href="{{ route('notifications.index') }}" class="min-w-0 flex-1">
                                                            <div class="text-sm font-medium text-[#0B1F3A]">
                                                                {{ data_get($notification->data, 'title', 'Portal notification') }}
                                                            </div>
                                                            <div class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">
                                                                {{ data_get($notification->data, 'message', 'Open notifications to review the latest portal activity.') }}
                                                            </div>
                                                        </a>
                                                        @unless ($notification->read())
                                                            <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}">
                                                                @csrf
                                                                <button type="submit" title="Mark as read" class="mt-0.5 inline-flex h-7 w-7 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:border-[#C8A24A] hover:bg-[#FFF9EA] hover:text-[#0B1F3A]">
                                                                    <span class="sr-only">Mark as read</span>
                                                                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                                        <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.25 7.32a1 1 0 0 1-1.42.002L3.29 9.776a1 1 0 1 1 1.334-1.49l4.04 3.617 6.62-6.688a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd" />
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        @endunless
                                                    </div>
                                                @empty
                                                    <div class="px-4 py-6 text-sm text-slate-500">
                                                        No notifications yet.
                                                    </div>
                                                @endforelse
                                            </div>

                                            <div class="grid grid-cols-2 border-t border-slate-100">
                                                <a href="{{ route('notifications.index') }}" class="px-4 py-3 text-center text-sm font-semibold text-[#0B1F3A] hover:bg-slate-50">View All</a>
                                                <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="border-l border-slate-100">
                                                    @csrf
                                                    <button type="submit" class="block w-full px-4 py-3 text-center text-sm font-semibold text-[#0B1F3A] hover:bg-slate-50">Mark All Read</button>
                                                </form>
                                            </div>
                                        </div>
                                    </x-slot>
                                </x-dropdown>

                                <x-dropdown align="right" width="64" contentClasses="bg-white p-0">
                                    <x-slot name="trigger">
                                        <button type="button" class="flex items-center gap-2 rounded-full border border-slate-200 py-1 pl-1 pr-2 transition hover:border-[#C8A24A] hover:bg-slate-50 sm:pr-3">
                                            <x-user-avatar :user="$user" size="sm" class="!h-8 !w-8" />
                                            <span class="hidden text-left sm:block">
                                                <span class="block max-w-32 truncate text-sm font-semibold text-[#0B1F3A]">{{ $user->name }}</span>
                                                <span class="block max-w-32 truncate text-xs text-slate-500">{{ $user->topbarRankRoleLabel() }}</span>
                                            </span>
                                            <svg class="hidden h-4 w-4 text-slate-400 sm:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path d="m6 9 6 6 6-6"></path>
                                            </svg>
                                        </button>
                                    </x-slot>

                                    <x-slot name="content">
                                        <div class="w-64 max-w-[calc(100vw-2rem)]">
                                            <div class="border-b border-slate-100 px-4 py-3">
                                                <div class="truncate text-sm font-semibold text-[#0B1F3A]">{{ $user->name }}</div>
                                                <div class="truncate text-xs text-slate-500">{{ $user->email }}</div>
                                            </div>

                                            <div class="py-1">
                                                <x-dropdown-link :href="route('dashboard')">Dashboard</x-dropdown-link>
                                                <x-dropdown-link :href="route('profile.edit')">My Profile</x-dropdown-link>
                                                @can('view team')
                                                    <x-dropdown-link :href="route('team.directs')">My Team</x-dropdown-link>
                                                @endcan
                                                <x-dropdown-link :href="route('resources.index')">Resources</x-dropdown-link>

                                                @if ($user->hasAnyRole(['super-admin', 'admin', 'agency-owner', 'team-leader', 'certified-field-mentor', 'trainer']))
                                                    <x-dropdown-link :href="route('admin.index')">Admin Management</x-dropdown-link>
                                                @endif
                                            </div>

                                            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                                                @csrf
                                                <button type="submit" class="block w-full px-4 py-3 text-left text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                                    Log Out
                                                </button>
                                            </form>
                                        </div>
                                    </x-slot>
                                </x-dropdown>
                            @else
                                <a href="{{ route('login') }}" class="efg-button-primary">Login</a>
                            @endauth
                        </div>
                    </div>
                </header>

                <main class="flex-1 px-4 py-6 lg:px-8">
                    {{ $slot ?? '' }}
                    @yield('content')
                </main>
            </div>
        </div>

        @livewireScripts
        @include('layouts.partials.page-chrome')
    </body>
</html>
