<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateProfileInviteLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'efg_invite_link' => ['nullable', 'url', 'max:255'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        session()->flash('invite_link_feedback', [
            'type' => 'error',
            'message' => 'Please enter a valid Experior invite link.',
        ]);

        throw (new ValidationException($validator))
            ->errorBag('profileInviteLink')
            ->redirectTo(route('profile.edit'));
    }
}
