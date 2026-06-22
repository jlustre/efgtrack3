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

    protected function prepareForValidation(): void
    {
        $normalized = [];

        foreach (['efg_associate_id', 'efg_invite_link'] as $field) {
            if ($this->input($field) === '') {
                $normalized[$field] = null;
            }
        }

        if ($normalized !== []) {
            $this->merge($normalized);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $profileId = $this->user()->profile()->value('id');

        return [
            'efg_associate_id' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('profiles', 'efg_associate_id')->ignore($profileId),
            ],
            'efg_invite_link' => [
                'nullable',
                'string',
                'max:2048',
                'url',
                Rule::unique('profiles', 'efg_invite_link')->ignore($profileId),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'efg_invite_link.url' => 'Enter a valid Experior invite URL (including https://).',
            'efg_invite_link.unique' => 'This Experior invite link is already in use by another member.',
            'efg_associate_id.unique' => 'This EFG Associate ID is already in use by another member.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        session()->flash('efg_details_feedback', [
            'type' => 'error',
            'message' => 'Please correct the EFG details and try again.',
        ]);

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo(route('profile.edit'));
    }
}
