<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateProfileInviteLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'efg_associate_id' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('profiles', 'efg_associate_id')->ignore($this->user()->profile?->id),
            ],
            'efg_invite_link' => ['nullable', 'string', 'max:2048', 'url'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'efg_invite_link.url' => 'Enter a valid Experior invite URL (including https://).',
            'efg_associate_id.unique' => 'This EFG Associate ID is already in use by another member.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo(route('profile.edit'));
    }
}
