<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Support\LocationOptions;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => ['nullable', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:120'],
            'province' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'timezone' => ['nullable', 'string', 'max:120'],
            'best_contact_time' => ['nullable', 'string', Rule::in(array_keys(LocationOptions::contactTimes()))],
            'license_number' => ['nullable', 'string', 'max:100'],
            'efg_associate_id' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('profiles', 'efg_associate_id')->ignore($this->user()->profile?->id),
            ],
            'bio' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $country = $this->input('country');
            $province = $this->input('province');
            $timezone = $this->input('timezone');

            if (filled($country) && LocationOptions::resolveCountryReference($country) === null) {
                $validator->errors()->add('country', 'Select a valid country.');
            }

            if (filled($timezone) && LocationOptions::resolveTimezoneReference($timezone) === null) {
                $validator->errors()->add('timezone', 'Select a valid timezone.');
            }

            if (! filled($province)) {
                return;
            }

            $countryId = LocationOptions::resolveCountryReference($country);
            $countryName = is_string($country) && ! is_numeric($country) ? $country : null;

            if (LocationOptions::resolveStateProvinceReference($province, $countryId, $countryName) === null) {
                $validator->errors()->add('province', 'Select a valid province or state for the chosen country.');
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        session()->flash('profile_feedback', [
            'type' => 'error',
            'message' => 'Please correct the highlighted fields and try again.',
        ]);

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo(route('profile.edit', ['tab' => 'profile']));
    }
}
