<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <a href="{{ route('communications.campaigns.index') }}" class="text-sm font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">← Campaign Center</a>
        <h1 class="mt-4 text-2xl font-semibold text-[#0B1F3A]">Create campaign</h1>
    </div>

    <form wire:submit="save" class="space-y-4 rounded-2xl border border-[#0B1F3A]/10 bg-white p-6 shadow-sm">
        <div>
            <label class="mb-1 block text-sm font-semibold text-[#0B1F3A]">Campaign name</label>
            <input type="text" wire:model="name" class="w-full rounded-lg border-slate-300 text-sm">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-[#0B1F3A]">Type</label>
            <select wire:model="type" class="w-full rounded-lg border-slate-300 text-sm">
                @foreach ($campaignTypes as $code => $meta)
                    <option value="{{ $code }}">{{ $meta['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-semibold text-[#0B1F3A]">Starts</label>
                <input type="date" wire:model="starts_at" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-[#0B1F3A]">Ends</label>
                <input type="date" wire:model="ends_at" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-[#0B1F3A]">Description</label>
            <textarea wire:model="description" rows="3" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-[#0B1F3A]">Rules</label>
            <textarea wire:model="rules" rows="4" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-[#0B1F3A]">Prizes (one per line)</label>
            <textarea wire:model="prizes" rows="3" class="w-full rounded-lg border-slate-300 text-sm" placeholder="1st place: $500 gift card"></textarea>
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" wire:model="publish_announcement" class="rounded border-slate-300 text-[#C8A24A]">
            Publish campaign announcement to the hub
        </label>
        <button type="submit" class="rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-[#C8A24A]">Create campaign</button>
    </form>
</div>
