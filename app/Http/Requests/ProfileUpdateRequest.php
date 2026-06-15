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
<<<<<<< HEAD
            'province' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'timezone' => ['nullable', 'string', 'max:120'],
            'best_contact_time' => ['nullable', 'string', Rule::in(array_keys(LocationOptions::contactTimes()))],
            'license_number' => ['nullable', 'string', 'max:100'],
=======
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'state_province_id' => ['nullable', 'integer', 'exists:state_provinces,id'],
            'timezone_id' => ['nullable', 'integer', 'exists:timezones,id'],
            'best_contact_time' => ['nullable', 'string', Rule::in(array_keys(LocationOptions::contactTimes()))],
            'license_number' => ['nullable', 'string', 'max:100'],
            'efg_associate_id' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('profiles', 'efg_associate_id')->ignore($this->user()->profile?->id),
            ],
            'efg_invite_link' => [
                'nullable',
                'string',
                'max:255',
                'url',
                Rule::unique('profiles', 'efg_invite_link')->ignore($this->user()->profile?->id),
            ],
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
            'bio' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
<<<<<<< HEAD
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
=======
            $countryId = $this->integer('country_id') ?: null;
            $stateProvinceId = $this->integer('state_province_id') ?: null;

            if (! LocationOptions::isValidStateProvinceId($countryId, $stateProvinceId)) {
                $validator->errors()->add('state_province_id', 'Select a valid province or state for the chosen country.');
>>>>>>> 2ae99211b388cde4b56062c1cfbbc9ca81c523b0
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        session()->flash('profile_feedback', [
            'type' => 'error',
            'message' => 'Please correct the highlighted fields and try again.',
        ]);

        $redirectTo = $this->input('redirect_to') === 'dashboard'
            ? route('dashboard')
            : route('profile.edit', ['tab' => 'profile']);

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo($redirectTo);
    }
}
