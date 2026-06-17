@php
    $selectedKeys = $selectedKeys ?? [];
    $provincesByCountry = $locationOptions['provincesByCountry'] ?? [];
    $inputClass = $inputClass ?? 'mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]';
    $labelClass = $labelClass ?? 'text-xs font-semibold text-slate-600';
    $sectionClass = $sectionClass ?? 'rounded-lg border border-slate-200 bg-slate-50 p-3';
    $checkboxClass = $checkboxClass ?? 'mt-0.5 rounded border-gray-300 text-[#C8A24A] focus:ring-[#C8A24A]';
    $countryLabelClass = $countryLabelClass ?? 'text-xs font-semibold text-[#8A6A1F] mb-2';
    $optionLabelClass = $optionLabelClass ?? 'flex items-start gap-2 text-xs text-slate-700 cursor-pointer';
@endphp

<div class="{{ $sectionClass }}">
    <p class="{{ $labelClass }}">Licensed provinces / states</p>
    <p class="mt-1 mb-3 text-xs text-slate-500">
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
                <p class="{{ $countryLabelClass }}">{{ $country }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ($provinces as $provinceValue => $provinceLabel)
                        @php
                            $jurisdictionKey = \App\Support\LocationOptions::jurisdictionKey($country, $provinceValue);
                        @endphp
                        <label class="{{ $optionLabelClass }}">
                            <input
                                type="checkbox"
                                name="licensed_jurisdictions[]"
                                value="{{ $jurisdictionKey }}"
                                @checked(in_array($jurisdictionKey, $selectedKeys, true))
                                class="{{ $checkboxClass }}"
                            >
                            <span>{{ \App\Support\LocationOptions::formatJurisdictionLabel($country, $provinceValue) }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    @error('licensed_jurisdictions')
        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
    @enderror
    @error('licensed_jurisdictions.*')
        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
