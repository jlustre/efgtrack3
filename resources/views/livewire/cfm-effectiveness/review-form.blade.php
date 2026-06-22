<form wire:submit="submit" class="space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white/90 p-6 shadow-sm backdrop-blur-sm">
        <h2 class="text-lg font-semibold text-[#0B1F3A]">Anonymous Mentor Feedback</h2>
        <p class="mt-1 text-sm text-slate-600">Rate your CFM from 1 (needs improvement) to 5 (excellent). Individual responses are never shown to your mentor.</p>

        <div class="mt-6 space-y-5">
            @foreach ($questions as $question)
                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-medium text-[#0B1F3A]">{{ $question->question }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="ratings.{{ $question->id }}" value="{{ $i }}" class="peer sr-only">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-300 bg-white text-sm font-semibold text-slate-600 peer-checked:border-[#C8A24A] peer-checked:bg-[#C8A24A] peer-checked:text-[#0B1F3A]">{{ $i }}</span>
                            </label>
                        @endfor
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white/90 p-6 shadow-sm backdrop-blur-sm">
        <h3 class="text-lg font-semibold text-[#0B1F3A]">Open Feedback</h3>
        <div class="mt-4 space-y-4">
            <div>
                <label class="text-sm font-medium text-[#0B1F3A]">What helped you most?</label>
                <textarea wire:model="helpedMost" rows="3" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm"></textarea>
            </div>
            <div>
                <label class="text-sm font-medium text-[#0B1F3A]">What could be improved?</label>
                <textarea wire:model="improvements" rows="3" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm"></textarea>
            </div>
            <div>
                <label class="text-sm font-medium text-[#0B1F3A]">Additional comments</label>
                <textarea wire:model="comments" rows="3" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm"></textarea>
            </div>
            <div>
                <label class="text-sm font-medium text-[#0B1F3A]">Suggestions</label>
                <textarea wire:model="suggestions" rows="3" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm"></textarea>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="rounded-lg bg-[#0B1F3A] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#132F55]">
            Submit Anonymous Feedback
        </button>
    </div>
</form>
