@php
    $fieldId = trim(($fieldIdPrefix ?? '').'_'.$field['name'], '_');
    $value = $fieldValue($field['name']);
@endphp

<template x-if="contentMode === 'compose'">
    <div>
        <label for="{{ $fieldId }}" class="block text-sm font-semibold text-[#0B1F3A]">{{ $field['label'] }}</label>
        @if (! empty($field['help']))
            <p class="mt-1 text-xs leading-5 text-slate-500">{{ $field['help'] }}</p>
        @endif
        <input
            id="{{ $fieldId }}"
            name="{{ $field['name'] }}"
            type="text"
            value="{{ $value }}"
            class="mt-2 block w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-[#C8A24A] focus:ring-[#C8A24A]"
        >
        @error($field['name'])
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</template>
