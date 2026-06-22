<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <a href="{{ route('communications.campaigns.index') }}" class="text-sm font-semibold text-[#8A6A1F] hover:text-[#0B1F3A]">← Campaign Center</a>
        <h1 class="mt-4 text-2xl font-semibold text-[#0B1F3A]">Create event announcement</h1>
        <p class="mt-2 text-sm text-slate-600">Publish an event announcement and automatically create a linked calendar event with RSVP.</p>
    </div>

    <form wire:submit="save" class="space-y-4 rounded-2xl border border-[#0B1F3A]/10 bg-white p-6 shadow-sm">
        <div>
            <label class="mb-1 block text-sm font-semibold text-[#0B1F3A]">Event title</label>
            <input type="text" wire:model="title" class="w-full rounded-lg border-slate-300 text-sm">
            @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-[#0B1F3A]">Summary</label>
            <input type="text" wire:model="summary" class="w-full rounded-lg border-slate-300 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-[#0B1F3A]">Details</label>
            <textarea wire:model="body" rows="5" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
            @error('body') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-semibold text-[#0B1F3A]">Start date & time</label>
                <input type="datetime-local" wire:model="starts_at" class="w-full rounded-lg border-slate-300 text-sm">
                @error('starts_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold text-[#0B1F3A]">Location</label>
                <input type="text" wire:model="location" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
        </div>
        <div>
            <label class="mb-1 block text-sm font-semibold text-[#0B1F3A]">Meeting link</label>
            <input type="url" wire:model="meeting_link" class="w-full rounded-lg border-slate-300 text-sm" placeholder="https://zoom.us/...">
        </div>
        <button type="submit" class="rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-[#C8A24A]">Publish event</button>
    </form>
</div>
