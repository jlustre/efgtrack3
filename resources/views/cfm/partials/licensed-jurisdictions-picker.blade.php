@php
    $selectedKeys = $selectedKeys ?? [];
    $provincesByCountry = $locationOptions['provincesByCountry'] ?? [];
    $inputClass = $inputClass ?? 'mt-1 w-full bg-gray-800 border border-gray-700 rounded-xl px-3 py-2 text-gray-200 focus:border-amber-500 focus:outline-none';
    $labelClass = $labelClass ?? 'text-xs font-medium text-gray-400';
    $sectionClass = $sectionClass ?? 'rounded-xl border border-gray-800 bg-gray-900/40 p-3';
@endphp

<div class="{{ $sectionClass }}">
    <p class="{{ $labelClass }}">Licensed provinces / states</p>
    <p class="text-xs text-gray-500 mt-1 mb-3">
        Select every jurisdiction where you hold a life insurance license. Apprentices are matched to CFMs licensed in their province or state.
    </p>

    <div class="space-y-4 max-h-56 overflow-y-auto pr-1">
        @foreach ($locationOptions['countries'] ?? [] as $country)
            @php
                $provinces = $provincesByCountry[$country] ?? [];
            @endphp
            @if ($provinces === [])
                @continue
            @endif
            <div>
                <p class="text-xs font-semibold text-amber-400/90 mb-2">{{ $country }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ($provinces as $provinceValue => $provinceLabel)
                        @php
                            $jurisdictionKey = \App\Support\LocationOptions::jurisdictionKey($country, $provinceValue);
                        @endphp
                        <label class="flex items-start gap-2 text-xs text-gray-300 cursor-pointer">
                            <input
                                type="checkbox"
                                name="licensed_jurisdictions[]"
                                value="{{ $jurisdictionKey }}"
                                @checked(in_array($jurisdictionKey, $selectedKeys, true))
                                class="mt-0.5 rounded border-gray-600 bg-gray-800 text-amber-500 focus:ring-amber-500"
                            >
                            <span>{{ \App\Support\LocationOptions::formatJurisdictionLabel($country, $provinceValue) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    @error('licensed_jurisdictions')
        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
    @enderror
    @error('licensed_jurisdictions.*')
        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
    @enderror
</div>
