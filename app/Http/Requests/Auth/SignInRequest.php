<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignInRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_id' => ['required', 'string', 'max:64'],  // persistente no app
            'device_name' => ['nullable', 'string', 'max:80'],
            'platform' => ['nullable', 'in:android,ios'],
        ];
    }
}
