<?php

namespace App\Http\Requests;

use App\Http\Requests\Auth\AuthRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends AuthRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255', 'unique:users,username', 'alpha_dash'],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'max:20', 'unique:users,phone_number'],
            'referral_code' => ['nullable', 'string', 'max:50', 'exists:users,referral_code'],
            'how_did_you_hear_about_us' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'how_did_you_hear_about_us' => 'how did you hear about us',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'referral_code.exists' => 'Invalid referral code.',
        ]);
    }
}
