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
        @include('layouts.partials.page-chrome')
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

                        @auth
                            <form
                                method="GET"
                                action="{{ route('search.index') }}"
                                class="order-3 w-full sm:order-none sm:min-w-72 sm:max-w-md sm:flex-1 lg:max-w-xl"
                                x-data="globalSearch()"
                                x-on:keydown.escape.window="open = false"
                            >
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
                                        x-ref="searchInput"
                                        name="q"
                                        x-model="query"
                                        x-on:input="onInput()"
                                        x-on:focus="query.trim().length >= 2 && (open = true)"
                                        x-on:blur="closeSuggestions()"
                                        value="{{ request('q') }}"
                                        type="search"
                                        placeholder="Search members, training, resources..."
                                        autocomplete="off"
                                        class="h-10 w-full rounded-full border-slate-200 bg-slate-50 pl-10 pr-4 text-sm shadow-sm transition focus:border-[#C8A24A] focus:bg-white focus:ring-[#C8A24A]"
                                    >

                                    <div
                                        x-show="open && (loading || results.length > 0 || query.trim().length >= 2)"
                                        x-cloak
                                        class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-50 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl"
                                    >
                                        <template x-if="loading">
                                            <div class="px-4 py-3 text-sm text-slate-500">Searching...</div>
                                        </template>
                                        <template x-if="! loading && results.length === 0 && query.trim().length >= 2">
                                            <div class="px-4 py-3 text-sm text-slate-500">No quick matches. Press Enter for full results.</div>
                                        </template>
                                        <ul x-show="results.length > 0" class="max-h-80 divide-y divide-slate-100 overflow-y-auto">
                                            <template x-for="result in results" :key="result.url">
                                                <li>
                                                    <a
                                                        :href="result.url"
                                                        class="block px-4 py-3 transition hover:bg-[#FFF9EA]"
                                                    >
                                                        <p class="text-sm font-semibold text-[#0B1F3A]" x-text="result.title"></p>
                                                        <p class="mt-0.5 text-xs text-slate-500">
                                                            <span x-text="result.type"></span>
                                                            <span x-show="result.subtitle"> · </span>
                                                            <span x-text="result.subtitle"></span>
                                                        </p>
                                                    </a>
                                                </li>
                                            </template>
                                        </ul>
                                        <div class="border-t border-slate-100 bg-slate-50 px-4 py-2">
                                            <button type="submit" class="text-xs font-semibold text-[#8A6A1F] hover:underline">
                                                View all results
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @else
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
                        @endauth

                        <div class="ml-auto flex items-center gap-2 sm:gap-3">
                            @auth
                                <a
                                    href="{{ route('tasks.index') }}"
                                    class="efg-icon-btn-lg relative"
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

                                <livewire:notifications.notification-bell />

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

                @auth
                    <livewire:notifications.critical-alert-banner />
                    <livewire:communication.communication-critical-banner />
                @endauth

                <main class="flex-1 px-4 py-6 lg:px-8">
                    {{ $slot ?? '' }}
                    @yield('content')
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
