<div class="bg-gray-900/30 backdrop-blur-sm border border-gray-800 rounded-2xl p-5 mb-8">
    <div class="flex flex-wrap justify-between items-center mb-4">
        <h3 class="font-bold text-white">CFM Filters</h3>
        <button type="button" @click="showFilters = !showFilters" class="text-amber-400 text-sm hover:text-amber-300">Toggle Advanced</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="search" x-model="searchQuery" placeholder="Search by name, email, phone" class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-amber-500">
        <select x-model="hierarchyFilter" class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-gray-300 focus:outline-none focus:border-amber-500">
            <option>All Accessible</option>
            <option>My Hierarchy</option>
            <option>Other Hierarchy</option>
        </select>
        <select x-model="filterWorkload" class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-gray-300 focus:outline-none focus:border-amber-500">
            <option value="">All workload levels</option>
            <option value="available">Available</option>
            <option value="moderate">Moderate</option>
            <option value="busy">Busy</option>
            <option value="overloaded">Overloaded</option>
            <option value="unavailable">Unavailable</option>
        </select>
        <button type="button" @click="clearFilters()" class="bg-amber-600/20 text-amber-400 border border-amber-500/50 rounded-xl px-4 py-2 hover:bg-amber-600/30 transition">Clear Filters</button>
    </div>
    <div x-show="showFilters" x-transition x-cloak class="grid grid-cols-1 md:grid-cols-5 gap-3 mt-4">
        <select x-model="filterCountry" class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-gray-300">
            <option value="">All countries</option>
            @foreach($cfmManagementPayload['filterOptions']['countries'] ?? [] as $country)
                <option value="{{ $country }}">{{ $country }}</option>
            @endforeach
        </select>
        <input type="text" placeholder="State/Province" class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-gray-300 placeholder-gray-500" disabled title="Coming soon">
        <input type="text" placeholder="City" class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-gray-300 placeholder-gray-500" disabled title="Coming soon">
        <select class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-gray-300" disabled>
            <option>Timezone</option>
            @foreach($cfmManagementPayload['filterOptions']['timezones'] ?? [] as $tz)
                <option>{{ $tz }}</option>
            @endforeach
        </select>
        <select x-model="filterRank" class="bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-gray-300">
            <option value="">All ranks</option>
            @foreach($cfmManagementPayload['filterOptions']['ranks'] ?? [] as $rank)
                <option value="{{ $rank }}">{{ $rank }}</option>
            @endforeach
        </select>
    </div>
</div>
