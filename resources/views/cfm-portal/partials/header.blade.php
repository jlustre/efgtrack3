<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <p class="text-amber-400/80 text-sm font-medium tracking-wide uppercase">Certified Field Mentor</p>
        <h1 class="text-3xl md:text-4xl font-bold text-white mt-1">CFM Portal</h1>
        <p class="text-gray-400 mt-2">{{ $todayLabel }}</p>
    </div>

    @if ($portal['isAdminView'] && count($portal['cfmOptions']) > 0)
        <form method="GET" action="{{ route('cfm.portal') }}" class="flex items-center gap-3">
            <label for="cfm-select" class="text-sm text-gray-400 whitespace-nowrap">Viewing CFM:</label>
            <select
                id="cfm-select"
                name="cfm"
                onchange="this.form.submit()"
                class="rounded-xl bg-gray-900 border border-gray-700 text-white text-sm px-3 py-2 min-w-[220px] focus:border-amber-500 focus:ring-amber-500"
            >
                @foreach ($portal['cfmOptions'] as $option)
                    <option value="{{ $option['id'] }}" @selected($option['id'] === $portal['selectedCfmId'])>{{ $option['name'] }}</option>
                @endforeach
            </select>
        </form>
    @endif
</div>

@if ($portal['isAdminView'])
    <div class="mb-6 rounded-xl border border-blue-700/40 bg-blue-900/20 px-4 py-3 text-sm text-blue-200">
        Admin view — you are reviewing {{ $portal['cfmUser']->name }}'s CFM portal.
    </div>
@endif

<div class="flex items-center gap-4 mb-8">
    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-600 to-amber-800 flex items-center justify-center text-2xl font-bold text-black">
        {{ $profile['initials'] }}
    </div>
    <div>
        <h2 class="text-2xl font-bold text-white">{{ $profile['name'] }}</h2>
        <p class="text-amber-400">{{ $profile['rank'] }} · {{ $profile['rankName'] }}</p>
        <p class="text-sm text-gray-400 mt-1">{{ $profile['certificationStatus'] }} · {{ $profile['workloadStatus'] }}</p>
    </div>
</div>
