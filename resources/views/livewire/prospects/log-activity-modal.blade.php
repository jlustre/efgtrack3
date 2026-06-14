<div>
    @if ($show)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-[#0B1F3A]/60" wire:click="close"></div>
            <div class="relative z-10 w-full max-w-lg rounded-lg border border-[#C8A24A]/40 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-[#0B1F3A]">Log Activity</h3>
                    <button type="button" wire:click="close" class="text-slate-500 hover:text-slate-700">&times;</button>
                </div>

                <form wire:submit="save" class="grid gap-3">
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Activity Type</span>
                        <select wire:model="activity_type" class="mt-1 block w-full rounded-lg border-slate-300 text-sm">
                            @foreach ($activityTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Date & Time</span>
                        <input wire:model="activity_occurred_at" type="datetime-local" class="mt-1 block w-full rounded-lg border-slate-300 text-sm">
                        @error('activity_occurred_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Outcome</span>
                        <input wire:model="activity_outcome" class="mt-1 block w-full rounded-lg border-slate-300 text-sm">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Notes</span>
                        <textarea wire:model="activity_notes" rows="2" class="mt-1 block w-full rounded-lg border-slate-300 text-sm"></textarea>
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Next Action</span>
                        <input wire:model="activity_next_action" class="mt-1 block w-full rounded-lg border-slate-300 text-sm">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-slate-700">Next Follow-Up</span>
                        <input wire:model="activity_next_follow_up_at" type="datetime-local" class="mt-1 block w-full rounded-lg border-slate-300 text-sm">
                    </label>
                    <div class="mt-2 flex justify-end gap-2">
                        <button type="button" wire:click="close" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Cancel</button>
                        <button type="submit" class="rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white">Save Activity</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
