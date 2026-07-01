@php

    $actions = $home['quick_actions'] ?? [];



    $iconFor = fn (string $icon): string => match ($icon) {

        'tasks' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',

        'prospects' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',

        'log_activity' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',

        'training' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',

        'messages' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',

        'goals' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',

        'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',

        'bookings' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',

        'resources' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',

        'profile' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',

        default => 'M13 7l5 5m0 0l-5 5m5-5H6',

    };



    $cardClasses = 'group flex w-full items-start gap-3 rounded-lg border border-slate-300 bg-slate-100 p-4 text-left shadow-sm transition hover:border-[#C8A24A]/50 hover:bg-[#FFF9EA]';

@endphp



@if ($actions !== [])

    <section>

        <h2 class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Quick Actions</h2>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

            @foreach ($actions as $action)

                @if (filled($action['action'] ?? null))

                    <button

                        type="button"

                        onclick="Livewire.dispatch(@js($action['action']))"

                        class="{{ $cardClasses }}"

                    >

                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#0B1F3A]/10 text-[#0B1F3A] transition group-hover:bg-[#C8A24A]/25">

                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">

                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconFor($action['icon'] ?? 'default') }}" />

                            </svg>

                        </span>

                        <span class="min-w-0">

                            <span class="block text-sm font-semibold text-[#0B1F3A] group-hover:text-[#C8A24A]">{{ $action['label'] }}</span>

                            <span class="mt-0.5 block text-xs text-slate-500">{{ $action['description'] }}</span>

                        </span>

                    </button>

                @else

                    <a href="{{ $action['route'] }}" class="{{ $cardClasses }}">

                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#0B1F3A]/10 text-[#0B1F3A] transition group-hover:bg-[#C8A24A]/25">

                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">

                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconFor($action['icon'] ?? 'default') }}" />

                            </svg>

                        </span>

                        <span class="min-w-0">

                            <span class="block text-sm font-semibold text-[#0B1F3A] group-hover:text-[#C8A24A]">{{ $action['label'] }}</span>

                            <span class="mt-0.5 block text-xs text-slate-500">{{ $action['description'] }}</span>

                        </span>

                    </a>

                @endif

            @endforeach

        </div>

    </section>

@endif

