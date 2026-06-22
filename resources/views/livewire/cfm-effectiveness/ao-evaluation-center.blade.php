<form wire:submit="submit" class="space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white/90 p-6 shadow-sm backdrop-blur-sm">
        <h2 class="text-lg font-semibold text-[#0B1F3A]">Quarterly CFM Evaluation</h2>
        <p class="mt-1 text-sm text-slate-600">Agency Owner scorecard — rate each category from 1 to 5.</p>

        <div class="mt-4">
            <label class="text-sm font-medium text-[#0B1F3A]">Select CFM</label>
            <select wire:model="cfmId" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm">
                <option value="">Choose a CFM...</option>
                @foreach ($cfms as $cfm)
                    <option value="{{ $cfm->id }}">{{ $cfm->name }}</option>
                @endforeach
            </select>
            @error('cfmId') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6 space-y-4">
            @foreach ($categories as $key => $label)
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-medium text-[#0B1F3A]">{{ $label }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="categoryScores.{{ $key }}" value="{{ $i }}" class="peer sr-only">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-300 bg-white text-sm font-semibold text-slate-600 peer-checked:border-[#C8A24A] peer-checked:bg-[#C8A24A] peer-checked:text-[#0B1F3A]">{{ $i }}</span>
                            </label>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white/90 p-6 shadow-sm backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-[#0B1F3A]">Comments & Potential</h3>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div><label class="text-sm font-medium">Strengths</label><textarea wire:model="strengths" rows="3" class="mt-1 w-full rounded-lg border-slate-300"></textarea></div>
            <div><label class="text-sm font-medium">Improvement Areas</label><textarea wire:model="improvementAreas" rows="3" class="mt-1 w-full rounded-lg border-slate-300"></textarea></div>
            <div><label class="text-sm font-medium">Recommendations</label><textarea wire:model="recommendations" rows="3" class="mt-1 w-full rounded-lg border-slate-300"></textarea></div>
            <div><label class="text-sm font-medium">Promotion Potential</label><input wire:model="promotionPotential" class="mt-1 w-full rounded-lg border-slate-300" placeholder="High / Medium / Developing"></div>
            <div class="md:col-span-2"><label class="text-sm font-medium">Leadership Potential</label><input wire:model="leadershipPotential" class="mt-1 w-full rounded-lg border-slate-300" placeholder="High / Medium / Developing"></div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="rounded-lg bg-[#C8A24A] px-5 py-2.5 text-sm font-semibold text-[#0B1F3A] hover:bg-[#FFF9EA]">Submit Evaluation</button>
    </div>
</form>
