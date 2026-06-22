@props(['title' => 'CFM Effectiveness'])

<x-app-layout>
    <div class="space-y-6">
        <div class="overflow-visible rounded-xl border border-[#C8A24A]/30 bg-gradient-to-br from-[#0B1F3A] via-[#132F55] to-[#0B1F3A] p-6 text-white shadow-lg">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Mentorship Quality Assurance</p>
                    <h1 class="mt-2 text-3xl font-semibold">{{ $title }}</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-200">
                        Coaching excellence, trainee development analytics, and leadership effectiveness — built for improvement, not popularity.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 pb-1">
                    @can('view CFM effectiveness')
                        <a href="{{ route('cfm.effectiveness.index') }}" class="rounded-lg px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('cfm.effectiveness.index') ? 'bg-[#C8A24A] text-[#0B1F3A]' : 'bg-white/10 text-white hover:bg-white/20' }}">Dashboard</a>
                    @endcan
                    @can('view own mentor feedback requests')
                        <a href="{{ route('cfm.effectiveness.reviews') }}" class="rounded-lg px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('cfm.effectiveness.reviews*') ? 'bg-[#C8A24A] text-[#0B1F3A]' : 'bg-white/10 text-white hover:bg-white/20' }}">My Reviews</a>
                    @endcan
                    @can('manage CFM evaluations')
                        <a href="{{ route('cfm.effectiveness.evaluations') }}" class="rounded-lg px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('cfm.effectiveness.evaluations') ? 'bg-[#C8A24A] text-[#0B1F3A]' : 'bg-white/10 text-white hover:bg-white/20' }}">AO Evaluations</a>
                    @endcan
                    @canany(['view CFM effectiveness', 'view CFM reports'])
                        <a href="{{ route('cfm.effectiveness.reports') }}" class="rounded-lg px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('cfm.effectiveness.reports*') ? 'bg-[#C8A24A] text-[#0B1F3A]' : 'bg-white/10 text-white hover:bg-white/20' }}">Reports</a>
                    @endcanany
                    @can('view CFM effectiveness')
                        <a href="{{ route('cfm.effectiveness.improvement') }}" class="rounded-lg px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('cfm.effectiveness.improvement') ? 'bg-[#C8A24A] text-[#0B1F3A]' : 'bg-white/10 text-white hover:bg-white/20' }}">Improvement</a>
                        <a href="{{ route('cfm.effectiveness.leaderboard') }}" class="rounded-lg px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('cfm.effectiveness.leaderboard') ? 'bg-[#C8A24A] text-[#0B1F3A]' : 'bg-white/10 text-white hover:bg-white/20' }}">Leaderboard</a>
                        <a href="{{ route('cfm.effectiveness.recognition') }}" class="rounded-lg px-3 py-2 text-sm font-semibold transition {{ request()->routeIs('cfm.effectiveness.recognition') ? 'bg-[#C8A24A] text-[#0B1F3A]' : 'bg-white/10 text-white hover:bg-white/20' }}">Recognition</a>
                    @endcan
                </div>
            </div>
        </div>

        @if (session('cfm_effectiveness_status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('cfm_effectiveness_status') }}
            </div>
        @endif

        {{ $slot }}
    </div>
</x-app-layout>
