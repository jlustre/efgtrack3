<div>
    <form wire:submit="save" class="rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-6 shadow-sm">
        @include('livewire.prospects.partials.form-fields', ['includeStatus' => true])

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('team.prospects.records.show', $prospect) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
            <button type="submit" class="rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#12345B]">Save Changes</button>
        </div>
    </form>
</div>
