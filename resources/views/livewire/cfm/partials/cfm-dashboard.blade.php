@php
    $profile = $portal['profile'];
    $training = $portal['training'];
@endphp

<div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-[#0B1F3A]">Profile</h3>
            @if ($portal['canEditProfile'])
                <button
                    type="button"
                    @click="showEditProfileModal = true"
                    class="inline-flex items-center rounded-lg border border-[#C8A24A]/40 px-3 py-1.5 text-xs font-semibold text-[#8A6A1F] transition hover:bg-[#FFF9EA]"
                >
                    Edit
                </button>
            @endif
        </div>

        <div class="mb-5 grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
            <div><span class="text-slate-500">Email:</span> <span class="text-[#0B1F3A]">{{ $profile['email'] }}</span></div>
            <div><span class="text-slate-500">Phone:</span> <span class="text-[#0B1F3A]">{{ $profile['phone'] }}</span></div>
            <div><span class="text-slate-500">Location:</span> <span class="text-[#0B1F3A]">{{ $profile['city'] }}, {{ $profile['province'] }}, {{ $profile['country'] }}</span></div>
            <div><span class="text-slate-500">Timezone:</span> <span class="text-[#0B1F3A]">{{ $profile['timezone'] }}</span></div>
            <div><span class="text-slate-500">Agency Owner:</span> <span class="text-[#0B1F3A]">{{ $profile['agencyOwner'] }}</span></div>
            <div><span class="text-slate-500">Last Activity:</span> <span class="text-[#0B1F3A]">{{ $profile['lastActivity'] }}</span></div>
            <div class="sm:col-span-2"><span class="text-slate-500">Languages:</span> <span class="text-[#0B1F3A]">{{ implode(', ', $profile['languages'] ?: ['—']) }}</span></div>
            <div class="sm:col-span-2"><span class="text-slate-500">Specialties:</span> <span class="text-[#0B1F3A]">{{ implode(', ', $profile['specialties'] ?: ['—']) }}</span></div>
            <div class="sm:col-span-2">
                <span class="text-slate-500">Licensed jurisdictions:</span>
                <span class="text-[#0B1F3A]">{{ $profile['licensedJurisdictionsLabel'] ?? '—' }}</span>
            </div>
        </div>

        @if ($profile['bio'])
            <p class="border-t border-slate-200 pt-4 text-sm leading-relaxed text-slate-600">{{ $profile['bio'] }}</p>
        @endif
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-[#0B1F3A]">AI Coaching Assistant</h3>
            <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[0.65rem] font-bold uppercase text-emerald-800">Live</span>
        </div>
        <ul class="space-y-3">
            @foreach ($aiPriorities as $priority)
                <li class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2.5 text-sm text-slate-700">
                    @if ($priority['trainee_id'])
                        <button type="button" wire:click="selectTrainee({{ $priority['trainee_id'] }}); setSection('assistant')" class="text-left font-semibold text-[#0B1F3A] hover:text-[#8A6A1F]">{{ $priority['message'] }}</button>
                    @else
                        {{ $priority['message'] }}
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    @include('cfm-portal.partials.training')

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-semibold text-[#0B1F3A]">Quick Actions</h3>
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
            <a href="{{ route('cfm-training.index') }}" class="rounded-lg border border-slate-200 px-4 py-3 text-sm font-semibold text-[#0B1F3A] transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">Open CFM Training</a>
            <a href="{{ route('calendar.index') }}" class="rounded-lg border border-slate-200 px-4 py-3 text-sm font-semibold text-[#0B1F3A] transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">My Calendar</a>
            <a href="{{ route('bookings.dashboard') }}" class="rounded-lg border border-slate-200 px-4 py-3 text-sm font-semibold text-[#0B1F3A] transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">Mentor Scheduling</a>
            <a href="{{ route('goals.coaching') }}" class="rounded-lg border border-slate-200 px-4 py-3 text-sm font-semibold text-[#0B1F3A] transition hover:border-[#C8A24A] hover:bg-[#FFF9EA]">Goals Coaching</a>
        </div>
    </div>
</div>

@include('cfm-portal.partials.rank-structure')

@if (count($profile['apprentices'] ?? []) > 0)
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-[#0B1F3A]">Trainee Mentoring Actions</h3>
            <span class="text-xs text-slate-500">{{ count($profile['apprentices']) }} active trainee(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-left text-slate-500">
                        <th class="pb-2 font-semibold">Name</th>
                        <th class="pb-2 font-semibold">Checklist</th>
                        <th class="pb-2 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach ($profile['apprentices'] as $trainee)
                        <tr wire:key="mentoring-row-{{ $trainee['id'] }}">
                            <td class="py-2.5 font-medium text-[#0B1F3A]">{{ $trainee['name'] }}</td>
                            <td class="py-2.5 text-xs text-slate-500">{{ $trainee['checklistPercent'] ?? 0 }}% complete</td>
                            <td class="space-x-2 py-2.5 text-right">
                                @if (! empty($trainee['assignmentId']))
                                    <button
                                        type="button"
                                        class="inline-flex rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-[#0B1F3A] transition hover:border-[#C8A24A] hover:text-[#8A6A1F]"
                                        @click="openTraineeChecklistModal(@js(route('cfm.portal.trainees.checklist', $trainee['assignmentId'])))"
                                    >
                                        View checklist
                                    </button>
                                    <a
                                        href="{{ route('cfm.portal.trainees.checklist', $trainee['assignmentId']) }}"
                                        class="inline-flex rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-[#0B1F3A] transition hover:border-[#C8A24A] hover:text-[#8A6A1F]"
                                    >
                                        Track mentoring
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    @include('cfm-portal.partials.calendar')
    @include('cfm-portal.partials.activity')
</div>
