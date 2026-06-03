<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-4">
    <div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-4 shadow-xl hover:shadow-2xl transition-all">
        <div class="text-amber-400 text-sm font-medium mb-2">Total CFMs</div>
        <div class="text-2xl font-bold text-white" x-text="stats.total"></div>
        <div class="text-xs text-gray-500 mt-1">Across accessible hierarchies</div>
    </div>
    <div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-4 shadow-xl">
        <div class="text-green-400 text-sm font-medium mb-2">Available CFMs</div>
        <div class="text-2xl font-bold text-green-400" x-text="stats.available"></div>
        <div class="text-xs text-gray-400">0–2 apprentices</div>
    </div>
    <div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-4 shadow-xl">
        <div class="text-orange-400 text-sm font-medium mb-2">Busy / Overloaded</div>
        <div class="text-2xl font-bold text-orange-400" x-text="busyOverloadedCount"></div>
        <div class="text-xs text-orange-300">6+ apprentices</div>
    </div>
    <div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-4 shadow-xl">
        <div class="text-amber-400 text-sm font-medium mb-2">My Hierarchy</div>
        <div class="text-2xl font-bold text-white" x-text="stats.myHierarchy"></div>
        <div class="text-xs text-amber-400">Full access</div>
    </div>
    <div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-4 shadow-xl">
        <div class="text-blue-400 text-sm font-medium mb-2">External Hierarchy</div>
        <div class="text-2xl font-bold text-blue-400" x-text="stats.externalHierarchy"></div>
        <div class="text-xs text-gray-400">May require approval</div>
    </div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-10">
    <div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-4">
        <div class="text-amber-400 text-sm font-medium mb-1">Active Apprentices</div>
        <div class="text-2xl font-bold text-white" x-text="stats.activeApprentices"></div>
    </div>
    <div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-4">
        <div class="text-amber-400 text-sm font-medium mb-1">Pending FAP Assignments</div>
        <div class="text-2xl font-bold text-white" x-text="stats.pendingFap"></div>
    </div>
    <div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-4">
        <div class="text-amber-400 text-sm font-medium mb-1">Avg Mentor Load</div>
        <div class="text-2xl font-bold text-white"><span x-text="stats.averageLoad"></span><span class="text-sm text-gray-400">/6</span></div>
    </div>
    <div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-4">
        <div class="text-amber-400 text-sm font-medium mb-1">FAP Completion Rate</div>
        <div class="text-2xl font-bold text-green-400"><span x-text="stats.fapCompletionRate"></span>%</div>
    </div>
    <div class="bg-gray-900/40 backdrop-blur-sm border border-gray-800 rounded-2xl p-4">
        <div class="text-amber-400 text-sm font-medium mb-1">Avg Weekly Availability</div>
        <div class="text-2xl font-bold text-white"><span x-text="stats.avgWeeklyAvailabilityHours"></span>h</div>
    </div>
</div>
