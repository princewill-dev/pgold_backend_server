<?php

namespace App\Http\Requests;

use App\Http\Requests\Auth\AuthRequest;

class VerifyOtpRequest extends AuthRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'otp' => ['required', 'string', 'size:6'],
            'reason' => ['required', 'string', 'in:registration,password_reset'],
        ];
    }
}
