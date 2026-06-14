<div>
    <div class="mb-4 grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-semibold text-slate-700">Save results to FNA</label>
            <select wire:model.live="fnaRecordId" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm">
                <option value="">— Select FNA record —</option>
                @foreach ($fnas as $fna)
                    <option value="{{ $fna->id }}">{{ $fna->reference_code }} — {{ $fna->client_name }}</option>
                @endforeach
            </select>
            @error('fnaRecordId')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @if ($saveStatus)
            <div class="flex items-end"><p class="text-sm font-semibold text-emerald-700">{{ $saveStatus }}</p></div>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            @include('livewire.fna.partials.dime-inputs')
            <div class="mt-6">
                <button type="button" wire:click="save" class="rounded-lg border border-[#C8A24A] bg-[#C8A24A] px-4 py-2 text-sm font-semibold text-[#0B1F3A] hover:bg-[#D8B85F]">Save to FNA</button>
            </div>
        </div>
        <div class="lg:col-span-1">
            @include('livewire.fna.partials.dime-result-panel', [
                'result' => $dimeResult,
                'gapSummary' => $gapSummary ?? null,
                'complianceNotice' => $complianceNotice ?? null,
            ])
        </div>
    </div>
</div>
