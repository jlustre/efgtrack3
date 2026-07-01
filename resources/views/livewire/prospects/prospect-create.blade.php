<div class="space-y-6">
    <div class="overflow-hidden rounded-lg border border-slate-400 bg-gradient-to-br from-white via-slate-50 to-[#FFF9EA] shadow-sm">
        <div class="bg-[#0B1F3A] px-6 py-6 text-white">
            <p class="text-sm font-semibold uppercase tracking-wide text-[#C8A24A]">Prospect Management</p>
            <h1 class="mt-2 text-2xl font-semibold">Add Prospect</h1>
            <p class="mt-2 text-sm text-slate-200">Create a new private prospect record and place them in your sales funnel.</p>
        </div>
    </div>

    <form wire:submit="save" class="rounded-lg border border-slate-400 bg-gradient-to-br from-white via-[#F8FAFC] to-[#FFF9EA] p-4 shadow-sm sm:p-5">
        @include('livewire.prospects.partials.form-fields')

        <div class="mt-4 flex justify-end gap-2">
            <a href="{{ route('team.prospects') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
            <button type="submit" class="rounded-lg bg-[#0B1F3A] px-4 py-2 text-sm font-semibold text-white hover:bg-[#12345B]">Create Prospect</button>
        </div>
    </form>
</div>
