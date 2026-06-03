<div
    x-show="showAssignModal"
    x-cloak
    class="fixed inset-0 z-[60] overflow-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
    @keydown.escape.window="showAssignModal = false"
>
    <div class="bg-gray-900 border border-gray-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl" @click.stop>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-bold text-white">Assign New Associate to CFM</h3>
                <p class="text-xs text-gray-500 mt-1">Select the associate first — only CFMs licensed in that province or state will appear. External hierarchy assignments may require approval.</p>
            </div>
            <button type="button" @click="showAssignModal = false" class="text-gray-400 hover:text-white text-2xl leading-none">&times;</button>
        </div>

        <div x-show="selectedCfm" class="mb-4 rounded-xl border border-amber-500/30 bg-amber-900/10 p-3 text-xs text-gray-300">
            <p class="font-semibold text-white" x-text="selectedCfm?.name"></p>
            <p x-text="'Load: ' + selectedCfm?.activeApprentices + '/' + selectedCfm?.maxApprentices + ' · ' + selectedCfm?.recommendationBand"></p>
        </div>

        <form method="POST" action="{{ route('team.cfms.assign') }}" class="space-y-3">
            @csrf

            <div>
                <label class="text-xs font-medium text-gray-400">Associate</label>
                <select
                    name="associate_id"
                    x-model="assignAssociateId"
                    @change="onAssignAssociateChange()"
                    required
                    class="mt-1 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-gray-200 focus:border-amber-500 focus:outline-none"
                >
                    <option value="">Select new associate</option>
                    <template x-for="a in assignableAssociates" :key="'assign-a-' + a.id">
                        <option
                            :value="a.id"
                            x-text="a.locationLabel ? a.name + ' (' + a.rank + ' · ' + a.locationLabel + ')' : a.name + ' (' + a.rank + ')'"
                        ></option>
                    </template>
                </select>
            </div>

            <p
                x-show="assignAssociateId && ! selectedAssignAssociate?.jurisdictionKey"
                class="text-sm text-amber-300 bg-amber-900/20 border border-amber-500/20 p-3 rounded-lg"
            >
                This associate has no province/state on file. Update their profile before assigning a CFM.
            </p>

            <div x-show="assignAssociateId && selectedAssignAssociate?.jurisdictionKey">
                <label class="text-xs font-medium text-gray-400">CFM (licensed in associate jurisdiction)</label>
                <select
                    name="cfm_id"
                    x-model="assignCfmId"
                    required
                    class="mt-1 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-gray-200 focus:border-amber-500 focus:outline-none"
                >
                    <option value="">Select CFM</option>
                    <template x-for="cfm in assignableCfmsForSelectedAssociate" :key="'assign-cfm-' + cfm.id">
                        <option :value="cfm.id" x-text="cfm.name + ' (' + cfm.activeApprentices + '/' + cfm.maxApprentices + ' · ' + (cfm.licensedJurisdictionsLabel || 'licensed') + ')'"></option>
                    </template>
                </select>
                <p
                    x-show="assignableCfmsForSelectedAssociate.length === 0"
                    class="mt-2 text-sm text-red-300 bg-red-900/20 border border-red-500/30 p-3 rounded-lg"
                >
                    No accessible CFM is licensed in <span class="font-semibold" x-text="selectedAssignAssociate?.locationLabel"></span>. Update a CFM's licensed jurisdictions or choose another associate.
                </p>
            </div>

            <input type="text" name="reason" x-model="assignForm.reason" value="{{ old('reason') }}" placeholder="Assignment reason" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-white placeholder-gray-500 focus:border-amber-500 focus:outline-none">

            <div class="grid grid-cols-2 gap-3">
                <input type="date" name="start_date" x-model="assignForm.startDate" value="{{ old('start_date') }}" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-gray-200">
                <input type="date" name="end_date" x-model="assignForm.endDate" value="{{ old('end_date') }}" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-gray-200">
            </div>

            <textarea name="notes" x-model="assignForm.notes" placeholder="Notes" rows="2" class="w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-white placeholder-gray-500 focus:border-amber-500 focus:outline-none">{{ old('notes') }}</textarea>

            <div class="flex flex-wrap gap-4 text-sm text-gray-300">
                <label class="flex items-center gap-2">
                    <input type="hidden" name="notify_cfm" value="0">
                    <input type="checkbox" name="notify_cfm" value="1" x-model="assignForm.notifyCfm" class="rounded border-gray-600 bg-gray-800 text-amber-500" @checked(old('notify_cfm', true))>
                    Notify CFM
                </label>
                <label class="flex items-center gap-2">
                    <input type="hidden" name="notify_associate" value="0">
                    <input type="checkbox" name="notify_associate" value="1" x-model="assignForm.notifyAssociate" class="rounded border-gray-600 bg-gray-800 text-amber-500" @checked(old('notify_associate', true))>
                    Notify associate
                </label>
                <label class="flex items-center gap-2">
                    <input type="hidden" name="require_cfm_approval" value="0">
                    <input type="checkbox" name="require_cfm_approval" value="1" x-model="assignForm.requireApproval" class="rounded border-gray-600 bg-gray-800 text-amber-500" @checked(old('require_cfm_approval', false))>
                    Require CFM approval
                </label>
            </div>

            <p class="text-xs text-gray-500">If you assign to an external hierarchy CFM, or check Require CFM approval, the assignment is saved as pending and the apprentice count will not increase until approved.</p>

            <div x-show="assignCfmId" x-text="getWorkloadWarning()" class="text-sm text-amber-300 bg-amber-900/20 border border-amber-500/20 p-3 rounded-lg"></div>

            <div
                x-show="assignCfmId && assignAssociateId && getLicenseMatchWarning()"
                x-text="getLicenseMatchWarning()"
                class="text-sm p-3 rounded-lg border text-emerald-300 bg-emerald-900/20 border-emerald-500/30"
            ></div>

            <div class="flex gap-3 pt-2">
                <button type="button" @click="showAssignModal = false" class="flex-1 border border-gray-700 py-2.5 rounded-xl text-gray-300 hover:bg-gray-800 transition">Cancel</button>
                <button
                    type="submit"
                    class="flex-1 bg-amber-600 text-black font-bold py-2.5 rounded-xl hover:bg-amber-500 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="! canSubmitAssignment"
                >
                    Submit Assignment
                </button>
            </div>
        </form>
    </div>
</div>
