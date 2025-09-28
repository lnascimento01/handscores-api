<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class MfaChallengeRequest extends FormRequest {
    public function rules(): array {
        return ['channel' => ['required','in:totp,sms,email,whatsapp']];
    }
}
