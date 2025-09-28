<?php

namespace App\Services;

use App\Models\MfaChallenge;
use App\Models\MfaFactor;
use App\Models\MfaRecoveryCode;
use App\Models\TrustedDevice;
use App\Models\User;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use OTPHP\TOTP;

class MfaService
{
    /**
     * Gera um TOTP novo (secret + otpauth-uri) e retorna ambos.
     * Salve o secret criptografado na MfaFactor.
     */
    public function generateTotp(User $user, ?string $label = null, ?string $issuer = null): array
    {
        $totp = TOTP::create(); // 30s, 6 dígitos, SHA1 (padrão)
        $totp->setLabel($label ?: $user->email);
        $totp->setIssuer($issuer ?: (config('app.name', 'Handscores')));

        $secret     = $totp->getSecret();           // Base32
        $otpauthUri = $totp->getProvisioningUri();  // otpauth://...

        return [$secret, $otpauthUri];
    }

    /**
     * Verifica código TOTP.
     */
    public function verifyTotp(string $secret, string $code): bool
    {
        $totp = TOTP::create($secret);
        $window = (int) config('mfa.window', 1); // aceita ±1 período
        return $totp->verify($code, null, $window);
    }

    /**
     * Gera PNG do QR Code a partir do otpauth-uri (base64 PNG).
     */
    public function makeQrPngBase64(string $otpauthUri, int $size = 320): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new ImagickImageBackEnd()
        );
        $writer = new Writer($renderer);
        $png = $writer->writeString($otpauthUri);

        return 'data:image/png;base64,' . base64_encode($png);
    }

    /**
     * Cria challenge OTP (totp/sms/email/whatsapp).
     * Para TOTP, apenas registra tentativa (não envia nada).
     */
    public function createChallenge(User $user, string $channel): MfaChallenge
    {
        $ttl = (int) config('mfa.ttl', 300);

        // Para canais não-TOTP, gerar um código de 6 dígitos
        $plainCode = null;
        if (in_array($channel, ['sms', 'email', 'whatsapp'])) {
            $plainCode = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        }

        // Se houver fator específico para o canal (sms/email/whatsapp)
        $factorId = MfaFactor::where('user_id', $user->id)
            ->where('type', $channel)
            ->where('verified', true)
            ->value('id');

        $challenge = MfaChallenge::create([
            'user_id'     => $user->id,
            'factor_id'   => $factorId,
            'channel'     => $channel,
            'code'        => $plainCode ? hash('sha256', $plainCode) : '', // TOTP não precisa
            'expires_at'  => now()->addSeconds($ttl),
            'attempts'    => 0,
            'ip'          => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'consumed'    => false,
        ]);

        // Anexe o código em cache por poucos segundos se desejar reusar em notification
        if ($plainCode) {
            Cache::put("mfa:challenge:plain:{$challenge->id}", $plainCode, now()->addSeconds(30));
        }

        return $challenge;
    }

    /**
     * Dispara envio do OTP para canais sms/email/whatsapp.
     * (Implemente suas Notifications/Jobs aqui)
     */
    public function dispatchOtp(MfaChallenge $challenge): void
    {
        if (!in_array($challenge->channel, ['sms', 'email', 'whatsapp'])) {
            return;
        }

        // Pegue código (visível) do cache
        $plain = Cache::pull("mfa:challenge:plain:{$challenge->id}");

        // Exemplo: você pode notificar via Mail/SMS/WA:
        // Notification::route('mail', $challenge->user->email)->notify(new OtpMail($plain));
        // Notification::route('nexmo', $phone)->notify(new OtpSms($plain));
        // Notification::route('whatsapp', $phone)->notify(new OtpWhatsapp($plain));
    }

    /**
     * Verifica código de challenge OU recovery code para o usuário.
     * - Se channel = totp, valida TOTP usando os fatores TOTP verificados.
     * - Caso contrário, compara hash do challenge armazenado.
     * - Tenta primeiro como recovery code para permitir fallback.
     */
    public function verifyCode(User $user, ?int $challengeId, string $code): bool
    {
        // 1) Tenta recovery code (hash persistido)
        $hash = hash('sha256', $code);
        $recovery = MfaRecoveryCode::where('user_id', $user->id)
            ->where('used', false)
            ->get();

        foreach ($recovery as $rc) {
            if (hash_equals($rc->code, $hash)) {
                $rc->update(['used' => true]);
                return true;
            }
        }

        // 2) Se challenge omitido, tentamos TOTP (canal totp)
        if (!$challengeId) {
            // Valida contra qualquer fator TOTP verificado do usuário
            $totpFactors = MfaFactor::where('user_id', $user->id)
                ->where('type', 'totp')
                ->where('verified', true)
                ->get();

            foreach ($totpFactors as $f) {
                $secret = decrypt($f->secret);
                if ($this->verifyTotp($secret, $code)) {
                    return true;
                }
            }
            return false;
        }

        // 3) Challenge presente: valida OTP do challenge
        $c = MfaChallenge::where('id', $challengeId)
            ->where('user_id', $user->id)
            ->first();

        if (!$c || $c->consumed || $c->expires_at->isPast()) {
            return false;
        }

        // TOTP via challenge? então valida TOTP
        if ($c->channel === 'totp') {
            $totpFactors = MfaFactor::where('user_id', $user->id)
                ->where('type', 'totp')->where('verified', true)->get();

            foreach ($totpFactors as $f) {
                $secret = decrypt($f->secret);
                if ($this->verifyTotp($secret, $code)) {
                    $c->update(['consumed' => true]);
                    return true;
                }
            }
            $c->increment('attempts');
            return false;
        }

        // Canais sms/email/whatsapp: compara hash
        $ok = hash_equals($c->code, $hash);
        if ($ok) {
            $c->update(['consumed' => true]);
        } else {
            $c->increment('attempts');
        }
        return $ok;
    }

    /**
     * Gera (e opcionalmente rotaciona) os recovery codes.
     * Retorna os códigos em claro para exibir ao usuário UMA vez.
     */
    public function generateRecoveryCodes(int $userId, bool $rotate = false): array
    {
        if ($rotate) {
            MfaRecoveryCode::where('user_id', $userId)->delete();
        }

        $count = (int) config('mfa.recovery_count', 10);
        $len   = (int) config('mfa.recovery_len', 5);

        $plainSet = [];
        $rows     = [];

        for ($i = 0; $i < $count; $i++) {
            $left  = Str::upper(Str::random($len));
            $right = Str::upper(Str::random($len));
            $plain = "{$left}-{$right}";
            $plainSet[] = $plain;

            $rows[] = [
                'user_id'    => $userId,
                'code'       => hash('sha256', $plain),
                'used'       => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        MfaRecoveryCode::insert($rows);
        return $plainSet; // mostre ao usuário e NÃO salve em claro
    }

    /**
     * Marca / atualiza dispositivo como confiável.
     */
    public function trustDevice(User $user, string $deviceId, ?string $platform = null, ?string $model = null, ?string $ip = null): TrustedDevice
    {
        $device = TrustedDevice::updateOrCreate(
            ['user_id' => $user->id, 'device_id' => $deviceId],
            [
                'platform'     => $platform,
                'model'        => $model,
                'ip'           => $ip,
                'last_seen_at' => now(),
            ]
        );

        if (!$device->verified_at) {
            $device->forceFill(['verified_at' => now()])->save();
        }

        return $device;
    }
}
