<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\MfaChallengeRequest;
use App\Http\Requests\Auth\MfaVerifyRequest;
use App\Models\MfaFactor;
use App\Models\TrustedDevice;
use App\Services\MfaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MfaController extends Controller
{
    public function enrollTotp(Request $req): JsonResponse
    {
        $user = $req->user();
        // gerar secret + otpauth uri
        [$secret, $otpauthUri] = app(MfaService::class)->generateTotp($user);

        [$secret, $otpauth] = app(MfaService::class)->generateTotp($user);

        $factor = MfaFactor::create([
            'user_id'   => $user->id,
            'type'      => 'totp',
            'label'     => 'Authenticator',
            'secret'    => encrypt($secret),
            'verified'  => false,
            'meta'      => ['otpauth_uri' => $otpauth],
        ]);

        $qrBase64 = app(MfaService::class)->makeQrPngBase64($otpauth);

        return response()->json([
            'factor_id'   => $factor->id,
            'otpauth_uri' => $otpauth,
            'qr_png_base64' => $qrBase64,
        ]);
    }

    public function confirmTotp(Request $req): JsonResponse
    {
        $data = $req->validate([
            'factor_id' => ['required', 'integer', 'exists:mfa_factors,id'],
            'code' => ['required', 'string', 'max:12'],
        ]);
        $factor = MfaFactor::whereKey($data['factor_id'])->where('type', 'totp')->firstOrFail();

        $ok = app(MfaService::class)->verifyTotp(decrypt($factor->secret), $data['code']);
        abort_unless($ok, 422, 'Invalid TOTP');

        $factor->forceFill(['verified' => true])->save();
        $recovery = app(MfaService::class)->generateRecoveryCodes($req->user()->id);

        return response()->json(['ok' => true, 'recovery_codes' => $recovery]);
    }

    public function challenge(MfaChallengeRequest $req): JsonResponse
    {
        $user = $req->user();
        $channel = $req->validated()['channel'];

        $challenge = app(MfaService::class)->createChallenge($user, $channel);
        // se SMS/email/whatsapp: notificar via queue
        app(MfaService::class)->dispatchOtp($challenge);

        return response()->json(['challenge_id' => $challenge->id, 'ttl' => config('mfa.ttl', 300)]);
    }

    public function verify(MfaVerifyRequest $req): JsonResponse
    {
        $user = $req->user();
        $data = $req->validated();

        $verified = app(MfaService::class)->verifyCode($user, $data['challenge_id'] ?? null, $data['code']);
        abort_unless($verified, 422, 'Invalid code');

        // marca trusted device
        TrustedDevice::where(['user_id' => $user->id, 'device_id' => $data['device_id']])
            ->update(['verified_at' => now(), 'last_seen_at' => now()]);

        // revoga token fraco atual e emite "forte"
        $req->user()->currentAccessToken()?->delete();

        $token = $user->createToken($data['device_id'], ['read', 'write', 'live:control'])->plainTextToken;

        // step-up: também pode gravar um carimbo no Redis com TTL
        Cache::put("mfa:stepup:{$user->id}:{$data['device_id']}", now()->timestamp, now()->addMinutes(15));

        return response()->json(['token' => $token]);
    }

    public function regenerateRecovery(Request $req): JsonResponse
    {
        app(MfaService::class)->generateRecoveryCodes($req->user()->id, true);
        return response()->json(['ok' => true]);
    }
}
