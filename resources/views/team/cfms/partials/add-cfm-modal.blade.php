<div
    x-show="showAddCfmModal"
    x-cloak
    class="fixed inset-0 z-[60] overflow-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4"
    @keydown.escape.window="showAddCfmModal = false"
>
    <div class="bg-gray-900 border border-gray-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl" @click.stop>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-bold text-white">Add Certified Field Mentor</h3>
                <p class="text-xs text-gray-500 mt-1">Nominate a team member for CFM certification and mentorship duties</p>
            </div>
            <button type="button" @click="showAddCfmModal = false" class="text-gray-400 hover:text-white text-2xl leading-none">&times;</button>
        </div>

        <form method="POST" action="{{ route('team.cfms.store') }}" class="space-y-3">
            @csrf

            <div>
                <label class="text-xs font-medium text-gray-400">Select team member</label>
                <select name="user_id" x-model="addCfmForm.userId" required class="mt-1 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-gray-200 focus:border-amber-500 focus:outline-none">
                    <option value="">Choose member…</option>
                    <template x-for="candidate in cfmCandidates" :key="'cand-' + candidate.id">
                        <option :value="candidate.id" x-text="candidate.name + ' (' + candidate.rank + ')'"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="text-xs font-medium text-gray-400">Target CFM rank tier</label>
                <select name="target_rank" x-model="addCfmForm.targetRank" class="mt-1 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-gray-200 focus:border-amber-500 focus:outline-none">
                    <option value="Associate Mentor">Associate Mentor</option>
                    <option value="CFM I">CFM I</option>
                    <option value="CFM II">CFM II</option>
                    <option value="Senior CFM">Senior CFM</option>
                </select>
            </div>

            <div>
                <label class="text-xs font-medium text-gray-400">Nomination notes</label>
                <textarea name="notes" x-model="addCfmForm.notes" rows="3" placeholder="Why is this member ready for CFM status?" class="mt-1 w-full bg-gray-800 border border-gray-700 rounded-xl px-4 py-2 text-white placeholder-gray-500 focus:border-amber-500 focus:outline-none">{{ old('notes') }}</textarea>
            </div>

            <div class="space-y-2 text-sm text-gray-300">
                <label class="flex items-center gap-2">
                    <input type="hidden" name="require_approval" value="0">
                    <input type="checkbox" name="require_approval" value="1" x-model="addCfmForm.requireApproval" class="rounded border-gray-600 bg-gray-800 text-amber-500">
                    Require admin approval before activation
                </label>
                <label class="flex items-center gap-2">
                    <input type="hidden" name="notify_candidate" value="0">
                    <input type="checkbox" name="notify_candidate" value="1" x-model="addCfmForm.notifyCandidate" class="rounded border-gray-600 bg-gray-800 text-amber-500">
                    Notify candidate by email
                </label>
            </div>

            <p x-show="cfmCandidates.length === 0" class="text-xs text-amber-300 bg-amber-900/20 border border-amber-500/20 rounded-lg p-3">No eligible team members found in your accessible downline.</p>

            <div class="flex gap-3 pt-2">
                <button type="button" @click="showAddCfmModal = false" class="flex-1 border border-gray-700 py-2.5 rounded-xl text-gray-300 hover:bg-gray-800 transition">Cancel</button>
                <button type="submit" class="flex-1 bg-amber-600 text-black font-bold py-2.5 rounded-xl hover:bg-amber-500 transition disabled:opacity-40 disabled:cursor-not-allowed">Submit Nomination</button>
            </div>
        </form>
    </div>
</div>
