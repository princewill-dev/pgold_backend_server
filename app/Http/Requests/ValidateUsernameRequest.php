<?php

namespace App\Http\Requests;

use App\Http\Requests\Auth\AuthRequest;

class ValidateUsernameRequest extends AuthRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255', 'alpha_dash'],
        ];
    }
}
