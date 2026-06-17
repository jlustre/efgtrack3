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

    <div class="mt-5 flex flex-wrap gap-3">
        <a href="{{ route('cfm-training.index') }}" class="inline-flex items-center rounded-lg bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:bg-[#D8B85F]">
            Open CFM Training
        </a>
        <a href="{{ route('calendar.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:bg-slate-50">
            My Calendar
        </a>
        <a href="{{ route('bookings.dashboard') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-[#0B1F3A] transition hover:bg-slate-50">
            Mentor Scheduling
        </a>
    </div>
</div>
