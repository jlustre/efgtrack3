<div class="bg-gradient-to-r from-gray-900 to-gray-800 border border-amber-500/30 rounded-2xl p-5 mb-10">
    <div class="flex items-center gap-2 flex-wrap">
        <h3 class="font-bold text-white">Smart Recommendations</h3>
        <span class="bg-amber-900/40 text-amber-300 text-xs px-2 py-0.5 rounded-full">Smart Match</span>
    </div>
    <p x-show="selectedRecommendationAssociate" class="text-xs text-gray-400 mt-2">
        Matches for <span class="text-white font-medium" x-text="selectedRecommendationAssociate?.name"></span>
        <span x-show="selectedRecommendationAssociate?.queueLabel" class="text-amber-300/80" x-text="' · ' + selectedRecommendationAssociate.queueLabel"></span>
        <button type="button" @click="openFapQueue()" class="ml-2 text-amber-400 hover:text-amber-300 underline">Change associate</button>
    </p>
    <p x-show="!selectedRecommendationAssociate && fapQueue.length === 0" class="text-xs text-gray-500 mt-2">
        No associates are waiting for CFM assignment.
    </p>

    <div x-show="aiSuggestions.filter(s => s.cfmName).length" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
        <template x-for="(item, idx) in aiSuggestions.filter(s => s.cfmName)" :key="'ai-' + idx">
            <div class="bg-gray-800/50 rounded-xl p-3 border-l-4" :class="aiBorderClass(item)">
                <div class="font-bold text-sm" :class="aiTitleClass(item)" x-text="item.statusLabel || item.label"></div>
                <div class="text-white text-sm mt-1" x-text="item.cfmName"></div>
                <div class="text-xs text-gray-400 mt-1">
                    <span x-show="item.fitScore" x-text="item.fitScore + '/100 fit · '"></span>
                    <span x-text="item.detail"></span>
                </div>
                <button type="button" @click="selectAiSuggestion(item)" class="mt-2 text-amber-400 text-xs hover:text-amber-300">Select</button>
            </div>
        </template>
    </div>

    <div x-show="!aiSuggestions.filter(s => s.cfmName).length && aiSuggestions.length" class="mt-4 rounded-xl border border-dashed border-gray-700 bg-gray-800/30 p-4 text-center">
        <p class="text-sm text-gray-400" x-text="aiSuggestions[0]?.detail || 'Select an associate from the FAP queue to see personalized CFM matches.'"></p>
        <button type="button" x-show="fapQueue.length" @click="openFapQueue()" class="mt-3 text-amber-400 text-xs hover:text-amber-300 underline">Open FAP Queue</button>
    </div>

    <ul class="mt-4 space-y-1">
        <template x-for="(item, idx) in aiSuggestions.filter(s => !s.cfmName)" :key="'ai-hint-' + idx">
            <li class="text-xs text-gray-500" x-text="'· ' + (item.detail || item.label)"></li>
        </template>
    </ul>
</div>
