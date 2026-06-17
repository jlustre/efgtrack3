<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateProfilePhotoRequest extends FormRequest
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
            'photo' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        session()->flash('profile_feedback', [
            'type' => 'error',
            'message' => 'Please choose a valid profile photo (JPEG, PNG, or WebP, up to 2 MB).',
        ]);

        if ($this->input('redirect_to') === 'dashboard') {
            session()->flash('show_profile_completion_modal', true);
        }

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->redirectTo(
                $this->input('redirect_to') === 'dashboard'
                    ? route('dashboard')
                    : route('profile.edit', ['tab' => 'profile'])
            );
    }
}
