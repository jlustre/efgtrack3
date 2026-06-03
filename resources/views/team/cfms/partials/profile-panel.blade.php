<div x-show="showProfilePanel" x-cloak class="fixed inset-0 z-50 overflow-hidden" @keydown.escape.window="showProfilePanel = false">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="showProfilePanel = false"></div>
    <div class="absolute right-0 top-0 h-full w-full max-w-2xl bg-gray-900 border-l border-gray-800 shadow-2xl overflow-y-auto">
        <div class="sticky top-0 bg-gray-900/95 backdrop-blur-sm p-4 border-b border-gray-800 flex justify-between items-center z-10">
            <h3 class="text-xl font-bold text-white">CFM Profile</h3>
            <button type="button" @click="showProfilePanel = false" class="text-gray-400 text-2xl hover:text-white leading-none">&times;</button>
        </div>
        <div class="p-6 space-y-5" x-show="selectedCfm">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-amber-600 to-amber-800 flex items-center justify-center text-2xl font-bold text-black" x-text="selectedCfm?.initials"></div>
                <div>
                    <div class="text-xl font-bold text-white" x-text="selectedCfm?.name"></div>
                    <div class="text-amber-400" x-text="selectedCfm?.rank"></div>
                    <div class="text-sm mt-1" :class="selectedCfm?.inMyHierarchy ? 'text-green-400' : 'text-blue-400'" x-text="selectedCfm?.hierarchy"></div>
                </div>
            </div>

            <p x-show="selectedCfm?.hierarchyNotice" class="bg-yellow-900/30 border border-yellow-600/50 rounded-xl p-3 text-sm text-yellow-200" x-text="selectedCfm?.hierarchyNotice"></p>
            <p x-show="selectedCfm?.limitedVisibility" class="text-xs text-gray-500">Limited visibility — apprentice details may be restricted for external CFMs.</p>

            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><span class="text-gray-400">Location:</span> <span class="text-gray-200" x-text="selectedCfm?.location"></span></div>
                <div><span class="text-gray-400">Timezone:</span> <span class="text-gray-200" x-text="selectedCfm?.timezone"></span></div>
                <div><span class="text-gray-400">Agency Owner:</span> <span class="text-gray-200" x-text="selectedCfm?.agencyOwner"></span></div>
                <div><span class="text-gray-400">Certification:</span> <span class="text-gray-200" x-text="selectedCfm?.certificationStatus"></span></div>
                <div class="col-span-2"><span class="text-gray-400">Languages:</span> <span class="text-gray-200" x-text="(selectedCfm?.languages || []).join(', ') || '—'"></span></div>
                <div class="col-span-2"><span class="text-gray-400">Specialty:</span> <span class="text-gray-200" x-text="(selectedCfm?.specialties || []).join(', ') || '—'"></span></div>
                <div class="col-span-2">
                    <span class="text-gray-400">Licensed jurisdictions:</span>
                    <span class="text-gray-200" x-text="selectedCfm?.licensedJurisdictionsLabel || '—'"></span>
                </div>
            </div>

            <div class="border border-gray-800 rounded-xl p-4" x-show="selectedCfm">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h4 class="font-semibold text-amber-400 text-sm">Licensed provinces / states</h4>
                    <button
                        type="button"
                        class="text-xs text-amber-400 hover:text-amber-300"
                        @click="showLicensedEdit ? closeLicensedEdit() : openLicensedEdit()"
                        x-text="showLicensedEdit ? 'Cancel' : 'Edit'"
                    ></button>
                </div>

                <div x-show="! showLicensedEdit" class="text-sm text-gray-400" x-text="selectedCfm?.licensedJurisdictionsLabel || 'None on file — add jurisdictions so apprentices can be matched by location.'"></div>

                <form
                    x-show="showLicensedEdit"
                    x-cloak
                    method="POST"
                    :action="licensedUpdateUrl"
                    class="space-y-3"
                    @submit="licensedSaving = true"
                >
                    @csrf
                    @method('PATCH')

                    @if ($cfmLicensedFeedback ?? null)
                        <div class="rounded-xl border px-4 py-3 text-sm {{ ($cfmLicensedFeedback['type'] ?? '') === 'success' ? 'border-emerald-500/30 bg-emerald-900/20 text-emerald-300' : 'border-red-500/30 bg-red-900/20 text-red-300' }}" role="alert">
                            <p class="font-semibold">{{ ($cfmLicensedFeedback['type'] ?? '') === 'success' ? 'Saved' : 'Could not save' }}</p>
                            <p class="mt-1">{{ $cfmLicensedFeedback['message'] }}</p>
                        </div>
                    @endif

                    @if ($errors->any() && ($openCfmLicensedEdit ?? false))
                        <div class="rounded-xl border border-red-500/30 bg-red-900/20 px-4 py-3 text-sm text-red-300" role="alert">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <p class="text-xs text-gray-500">Select every jurisdiction where this CFM holds a life insurance license.</p>

                    <div class="space-y-4 max-h-56 overflow-y-auto pr-1 rounded-xl border border-gray-800 bg-gray-900/40 p-3">
                        <template x-for="country in licensedCountries" :key="'lic-country-' + country">
                            <div>
                                <p class="text-xs font-semibold text-amber-400/90 mb-2" x-text="country"></p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <template x-for="entry in provincesForCountry(country)" :key="entry.key">
                                        <label class="flex items-start gap-2 text-xs text-gray-300 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                class="mt-0.5 rounded border-gray-600 bg-gray-800 text-amber-500 focus:ring-amber-500"
                                                :checked="licensedDraft.includes(entry.key)"
                                                @change="toggleLicensedJurisdiction(entry.key, $event.target.checked)"
                                            >
                                            <span x-text="entry.label"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <template x-for="key in licensedDraft" :key="'lic-hidden-' + key">
                        <input type="hidden" name="licensed_jurisdictions[]" :value="key">
                    </template>

                    <button
                        type="submit"
                        class="w-full bg-amber-600 hover:bg-amber-500 text-black font-bold py-2.5 rounded-xl transition disabled:opacity-60"
                        :disabled="licensedSaving"
                    >
                        <span x-show="! licensedSaving">Save licensed jurisdictions</span>
                        <span x-show="licensedSaving" x-cloak>Saving…</span>
                    </button>
                </form>
            </div>

            <p class="text-sm text-gray-400 leading-relaxed" x-text="selectedCfm?.bio"></p>

            <div class="border-t border-gray-800 pt-4">
                <h4 class="font-semibold text-amber-400">Workload &amp; Metrics</h4>
                <div class="flex flex-wrap gap-4 mt-2 text-sm text-gray-300">
                    <span>Active: <strong class="text-white" x-text="selectedCfm?.activeApprentices + '/' + selectedCfm?.maxApprentices"></strong></span>
                    <span>Completion: <strong class="text-green-400" x-text="selectedCfm?.completionRate + '%'"></strong></span>
                    <span>Score: <strong class="text-white" x-text="selectedCfm?.score + '/100'"></strong></span>
                    <span class="px-2 py-0.5 rounded-full text-xs" :class="statusBadgeClass(selectedCfm?.statusColor)" x-text="selectedCfm?.recommendationBand"></span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2 mt-3">
                    <div class="bg-amber-400 h-2 rounded-full" :style="selectedCfm ? 'width:' + loadWidth(selectedCfm) + '%' : 'width:0%'"></div>
                </div>
                <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-gray-500">
                    <span>Calendar busyness: <span class="text-gray-300" x-text="selectedCfm?.calendarBusyness + '%'"></span></span>
                    <span>Overdue tasks: <span :class="selectedCfm?.overdueTasks > 0 ? 'text-red-400' : 'text-gray-300'" x-text="selectedCfm?.overdueTasks"></span></span>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-4" x-show="selectedCfm?.calendarPreview">
                <h4 class="font-semibold text-amber-400">Calendar Preview</h4>
                <div class="text-sm mt-2 text-gray-300 space-y-1">
                    <p>Next available: <span class="text-white" x-text="selectedCfm?.nextAvailable"></span></p>
                    <p>Booked this week: <span x-text="selectedCfm?.calendarPreview?.bookedSessions"></span></p>
                    <p>Open slots this week: <span x-text="selectedCfm?.calendarPreview?.slotsThisWeek"></span></p>
                    <p>Upcoming sessions (7d): <span x-text="selectedCfm?.upcomingSessions"></span></p>
                    <p x-show="selectedCfm?.calendarPreview?.conflictWarning" class="text-red-400 font-medium">Calendar conflict warning</p>
                    <template x-for="slot in selectedCfm?.nextSlots || []" :key="slot">
                        <p class="text-xs text-gray-500" x-text="'· ' + slot"></p>
                    </template>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-4">
                <h4 class="font-semibold text-amber-400">Apprentice Breakdown</h4>
                <dl class="mt-2 grid grid-cols-2 gap-2 text-xs" x-show="selectedCfm?.apprenticeBreakdown">
                    <template x-for="(val, key) in selectedCfm.apprenticeBreakdown" :key="key">
                        <div class="flex justify-between rounded-lg bg-gray-800/50 px-2 py-1.5">
                            <span class="text-gray-500 capitalize" x-text="key.replace(/([A-Z])/g, ' $1')"></span>
                            <span class="text-white font-medium" x-text="val"></span>
                        </div>
                    </template>
                </dl>
            </div>

            <div class="border-t border-gray-800 pt-4">
                <h4 class="font-semibold text-amber-400">Active Apprentices</h4>
                <ul class="mt-2 space-y-1 text-sm text-gray-300">
                    <template x-for="a in selectedCfm?.apprentices || []" :key="a.id">
                        <li><span class="text-white" x-text="a.name"></span> · <span class="text-gray-500" x-text="a.rank"></span> · <span class="text-xs" x-text="a.status"></span></li>
                    </template>
                    <li x-show="!(selectedCfm?.apprentices?.length)" class="text-gray-500">No apprentices assigned</li>
                </ul>
            </div>

            <div class="border-t border-gray-800 pt-4">
                <h4 class="font-semibold text-amber-400">Recent Activity</h4>
                <ul class="mt-2 space-y-1 text-xs text-gray-400">
                    <template x-for="(item, i) in selectedCfm?.activityTimeline || []" :key="'act-' + i">
                        <li><span class="text-gray-300" x-text="item.label"></span> · <span x-text="item.time"></span></li>
                    </template>
                </ul>
            </div>

            <div class="border-t border-gray-800 pt-4">
                <h4 class="font-semibold text-amber-400">Assignment History</h4>
                <ul class="mt-2 space-y-1 text-xs text-gray-400">
                    <template x-for="(h, i) in selectedCfm?.assignmentHistory || []" :key="'hist-' + i">
                        <li x-text="h.apprentice + ' — ' + h.status + ' (' + h.date + ')'"></li>
                    </template>
                </ul>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" @click="openAssign(selectedCfm); showProfilePanel = false" class="flex-1 bg-amber-600 hover:bg-amber-500 text-black font-bold py-2.5 rounded-xl transition-all">
                    Assign Associate
                </button>
                <a :href="selectedCfm?.profileUrl" class="flex-1 text-center border border-gray-700 text-gray-300 hover:text-white py-2.5 rounded-xl text-sm transition">Full Profile</a>
            </div>
        </div>
    </div>
</div>
