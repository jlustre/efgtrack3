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
            'country' => ['nullable', 'string', Rule::in(LocationOptions::countries())],
            'timezone' => ['nullable', 'string', Rule::in(array_keys(LocationOptions::timezones()))],
            'best_contact_time' => ['nullable', 'string', Rule::in(array_keys(LocationOptions::contactTimes()))],
            'license_number' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $country = $this->input('country');
            $province = $this->input('province');

            if (filled($province) && ! LocationOptions::isValidProvince($country, $province)) {
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
