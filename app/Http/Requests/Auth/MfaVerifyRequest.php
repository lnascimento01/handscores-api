<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class MfaVerifyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'challenge_id' => ['nullable', 'integer'],
            'code' => ['required', 'string', 'max:12'],
            'device_id' => ['required', 'string', 'max:64'],
        ];
    }
}
