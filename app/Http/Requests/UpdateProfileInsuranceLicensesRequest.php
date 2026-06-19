<?php

namespace App\Http\Requests;

use App\Support\LocationOptions;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateProfileInsuranceLicensesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'insurance_licenses' => ['nullable', 'array'],
            'insurance_licenses.*' => ['string', 'max:120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach ($this->input('insurance_licenses', []) as $index => $key) {
                if (! is_string($key) || ! LocationOptions::isValidJurisdictionKey($key)) {
                    $validator->errors()->add(
                        "insurance_licenses.{$index}",
                        'Each license must be a valid province or state in a supported country.'
                    );
                }
            }
        });
    }

    /**
     * @return list<string>
     */
    public function normalizedInsuranceLicenses(): array
    {
        return LocationOptions::normalizeLicensedJurisdictionKeys(
            $this->input('insurance_licenses', [])
        );
    }

    protected function failedValidation(Validator $validator): void
    {
        session()->flash('licenses_feedback', [
            'type' => 'error',
            'message' => 'Please correct the selected licenses and try again.',
        ]);

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo(route('profile.edit', ['tab' => 'licenses']));
    }
}
