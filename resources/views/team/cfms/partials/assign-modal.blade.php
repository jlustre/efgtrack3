<div
    x-show="showAssignModal"
    x-cloak
    class="fixed inset-0 z-[60] flex items-center justify-center overflow-auto bg-slate-900/50 p-4 backdrop-blur-sm"
    @keydown.escape.window="showAssignModal = false"
>
    <div class="w-full max-w-lg rounded-xl border border-slate-200 bg-white p-6 shadow-xl" @click.stop>
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-xl font-semibold text-[#0B1F3A]">Assign New Associate to CFM</h3>
                <p class="mt-1 text-xs text-slate-500">Select the associate first — only CFMs licensed in that province or state will appear. External hierarchy assignments may require approval.</p>
            </div>
            <button type="button" @click="showAssignModal = false" class="text-2xl leading-none text-slate-400 hover:text-[#0B1F3A]">&times;</button>
        </div>

        <div x-show="selectedCfm" class="mb-4 rounded-lg border border-[#C8A24A]/30 bg-[#FFF9EA] p-3 text-xs text-slate-700">
            <p class="font-semibold text-[#0B1F3A]" x-text="selectedCfm?.name"></p>
            <p x-text="'Load: ' + selectedCfm?.activeApprentices + '/' + selectedCfm?.maxApprentices + ' · ' + selectedCfm?.recommendationBand"></p>
        </div>

        <form method="POST" action="{{ route('team.cfms.assign') }}" class="space-y-3">
            @csrf

            <div>
                <label class="text-xs font-semibold text-slate-600">Associate</label>
                <select
                    name="associate_id"
                    x-model="assignAssociateId"
                    @change="onAssignAssociateChange()"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
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
                class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900"
            >
                This associate has no province/state on file. Update their profile before assigning a CFM.
            </p>

            <div x-show="assignAssociateId && selectedAssignAssociate?.jurisdictionKey">
                <label class="text-xs font-semibold text-slate-600">CFM (licensed in associate jurisdiction)</label>
                <select
                    name="cfm_id"
                    x-model="assignCfmId"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
                >
                    <option value="">Select CFM</option>
                    <template x-for="cfm in assignableCfmsForSelectedAssociate" :key="'assign-cfm-' + cfm.id">
                        <option :value="cfm.id" x-text="cfm.name + ' (' + cfm.activeApprentices + '/' + cfm.maxApprentices + ' · ' + (cfm.licensedJurisdictionsLabel || 'licensed') + ')'"></option>
                    </template>
                </select>
                <p
                    x-show="assignableCfmsForSelectedAssociate.length === 0"
                    class="mt-2 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800"
                >
                    No accessible CFM is licensed in <span class="font-semibold" x-text="selectedAssignAssociate?.locationLabel"></span>. Update a CFM's licensed jurisdictions or choose another associate.
                </p>
            </div>

            <input type="text" name="reason" x-model="assignForm.reason" value="{{ old('reason') }}" placeholder="Assignment reason" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">

            <div class="grid grid-cols-2 gap-3">
                <input type="date" name="start_date" x-model="assignForm.startDate" value="{{ old('start_date') }}" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
                <input type="date" name="end_date" x-model="assignForm.endDate" value="{{ old('end_date') }}" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">
            </div>

            <textarea name="notes" x-model="assignForm.notes" placeholder="Notes" rows="2" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]">{{ old('notes') }}</textarea>

            <div class="flex flex-wrap gap-4 text-sm text-slate-700">
                <label class="flex items-center gap-2">
                    <input type="hidden" name="notify_cfm" value="0">
                    <input type="checkbox" name="notify_cfm" value="1" x-model="assignForm.notifyCfm" class="rounded border-gray-300 text-[#C8A24A] focus:ring-[#C8A24A]" @checked(old('notify_cfm', true))>
                    Notify CFM
                </label>
                <label class="flex items-center gap-2">
                    <input type="hidden" name="notify_associate" value="0">
                    <input type="checkbox" name="notify_associate" value="1" x-model="assignForm.notifyAssociate" class="rounded border-gray-300 text-[#C8A24A] focus:ring-[#C8A24A]" @checked(old('notify_associate', true))>
                    Notify associate
                </label>
                <label class="flex items-center gap-2">
                    <input type="hidden" name="require_cfm_approval" value="1">
                    <input type="checkbox" value="1" class="rounded border-gray-300 text-[#C8A24A]" checked disabled>
                    Require CFM confirmation
                </label>
            </div>

            <p class="text-xs text-slate-500">Assignments are saved as pending. The CFM receives a confirmation email and must accept before the member becomes an active trainee.</p>

            <div x-show="assignCfmId" x-text="getWorkloadWarning()" class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900"></div>

            <div
                x-show="assignCfmId && assignAssociateId && getLicenseMatchWarning()"
                x-text="getLicenseMatchWarning()"
                class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800"
            ></div>

            <div class="flex gap-3 pt-2">
                <button type="button" @click="showAssignModal = false" class="flex-1 rounded-lg border border-slate-300 py-2.5 text-sm font-semibold text-[#0B1F3A] transition hover:bg-slate-50">Cancel</button>
                <button
                    type="submit"
                    class="flex-1 rounded-lg bg-[#C8A24A] py-2.5 font-bold text-[#0B1F3A] transition hover:bg-[#D8B85F] disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="! canSubmitAssignment"
                >
                    Submit Assignment
                </button>
            </div>
        </form>
    </div>
</div>
