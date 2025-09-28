<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignInRequest;
use App\Models\TrustedDevice;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function signIn(SignInRequest $req): JsonResponse
    {
        $data = $req->validated();
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            RateLimiter::hit('login:' . $req->ip(), 60);
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // lockout progressivo
        if (RateLimiter::tooManyAttempts('login:' . $req->ip(), 10)) {
            return response()->json(['error' => 'Too many attempts'], 429);
        }

        // Token "fraco" inicial (somente leitura, sem live control)
        // Dica: nome do token = device_id
        $token = $user->createToken(
            $data['device_id'] ?? Str::uuid()->toString(),
            ['read'] // abilities mínimas até MFA
        )->plainTextToken;

        // registra/atualiza trusted device (ainda não verificado)
        TrustedDevice::updateOrCreate(
            ['user_id' => $user->id, 'device_id' => $data['device_id']],
            [
                'platform' => $data['platform'] ?? null,
                'model' => $data['device_name'] ?? null,
                'last_seen_at' => now()
            ]
        );

        return response()->json([
            'token' => $token,
            'mfa_enabled' => $user->mfaFactors()->where('verified', true)->exists()
        ]);
    }

    public function signOut(Request $req): JsonResponse
    {
        /** @var \Laravel\Sanctum\NewAccessToken $token */
        $req->user()->currentAccessToken()?->delete();
        return response()->json(['ok' => true]);
    }

    public function signUp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', Password::min(6)],
            // opcional: phone, etc
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // se quiser já logar e devolver token Sanctum:
        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'access_token' => $token,
            'mfa_required' => false,
        ], 201);
    }
}
