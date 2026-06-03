<div class="lg:col-span-2 bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-amber-400">Profile</h3>
        @if ($portal['canEditProfile'])
            <button
                type="button"
                @click="showEditProfileModal = true"
                class="inline-flex items-center rounded-xl border border-amber-500/40 px-3 py-1.5 text-xs font-semibold text-amber-400 hover:bg-amber-500/10 transition"
            >
                Edit
            </button>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-5">
        <div><span class="text-gray-500">Email:</span> <span class="text-gray-200">{{ $profile['email'] }}</span></div>
        <div><span class="text-gray-500">Phone:</span> <span class="text-gray-200">{{ $profile['phone'] }}</span></div>
        <div><span class="text-gray-500">Location:</span> <span class="text-gray-200">{{ $profile['city'] }}, {{ $profile['province'] }}, {{ $profile['country'] }}</span></div>
        <div><span class="text-gray-500">Timezone:</span> <span class="text-gray-200">{{ $profile['timezone'] }}</span></div>
        <div><span class="text-gray-500">Agency Owner:</span> <span class="text-gray-200">{{ $profile['agencyOwner'] }}</span></div>
        <div><span class="text-gray-500">Last Activity:</span> <span class="text-gray-200">{{ $profile['lastActivity'] }}</span></div>
        <div class="sm:col-span-2"><span class="text-gray-500">Languages:</span> <span class="text-gray-200">{{ implode(', ', $profile['languages'] ?: ['—']) }}</span></div>
        <div class="sm:col-span-2"><span class="text-gray-500">Specialties:</span> <span class="text-gray-200">{{ implode(', ', $profile['specialties'] ?: ['—']) }}</span></div>
        <div class="sm:col-span-2">
            <span class="text-gray-500">Licensed jurisdictions:</span>
            <span class="text-gray-200">{{ $profile['licensedJurisdictionsLabel'] ?? '—' }}</span>
        </div>
    </div>

    @if ($profile['bio'])
        <p class="text-sm text-gray-400 leading-relaxed border-t border-gray-800 pt-4">{{ $profile['bio'] }}</p>
    @endif

    <div class="mt-5 flex flex-wrap gap-3">
        <a href="{{ route('cfm-training.index') }}" class="inline-flex items-center rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-black hover:bg-amber-400 transition">
            Open CFM Training
        </a>
        <a href="{{ route('calendar.index') }}" class="inline-flex items-center rounded-xl border border-gray-700 px-4 py-2 text-sm font-medium text-gray-200 hover:bg-gray-800 transition">
            My Calendar
        </a>
        <a href="{{ route('bookings.dashboard') }}" class="inline-flex items-center rounded-xl border border-gray-700 px-4 py-2 text-sm font-medium text-gray-200 hover:bg-gray-800 transition">
            Mentor Scheduling
        </a>
    </div>
</div>
