<?php

declare(strict_types=1);

namespace App\Domain\Identity\Web\Actions\Auth;

use App\Domain\Identity\Data\LoginData;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Lorisleiva\Actions\Concerns\AsAction;
use ReCaptcha\ReCaptcha;

final class Login
{
    use AsAction;
    use HtmxOrchestrator;

    private const string BLOCK_PREFIX = 'login_blocked:';

    private const string ATTEMPTS_PREFIX = 'login_attempts:';

    public function handle(LoginData $data): bool
    {
        return Auth::attempt(
            credentials: ['email' => $data->email, 'password' => $data->password],
            remember: $data->remember,
        );
    }

    public function asController(Request $request): JsonResponse
    {
        $data = LoginData::from($request->all());

        if (blank($data->password)) {
            return $this->hxNotify('La contraseña es obligatoria.', 'error')->hxResponse();
        }

        $ip = $request->ip() ?? '0.0.0.0';
        $key = $this->throttleKey($data->email, $ip);

        // 1. ¿IP+email bloqueados por demasiados intentos?
        if (($blocked = $this->checkIfBlocked($key)) instanceof JsonResponse) {
            return $blocked;
        }

        // 2. Rate limit
        if (($limited = $this->checkRateLimit($key)) instanceof JsonResponse) {
            return $limited;
        }

        // 3. reCAPTCHA
        if (($captcha = $this->verifyRecaptcha($data->recaptcha_token, $ip)) instanceof JsonResponse) {
            return $captcha;
        }

        // 4. Intento de autenticación
        if (! $this->handle($data)) {
            return $this->handleFailedAttempt($key, $data->email, $ip);
        }

        // 5. Éxito
        session()->regenerate();
        RateLimiter::clear($key);
        Cache::forget(self::BLOCK_PREFIX.$key);
        Cache::forget(self::ATTEMPTS_PREFIX.$key);

        Log::info('Login exitoso', [
            'email' => $this->maskEmail($data->email),
            'ip' => $ip,
        ]);

        return $this
            ->hxNotify('Bienvenido a SIGMA', 'success')
            ->hxRedirect(route('home'));
    }

    private function throttleKey(string $email, string $ip): string
    {
        return mb_strtolower($email).'|'.$ip;
    }

    private function checkIfBlocked(string $key): ?JsonResponse
    {
        if (! Cache::has(self::BLOCK_PREFIX.$key)) {
            return null;
        }

        $val = config('auth.login_decay_minutes', 15);
        $minutes = is_numeric($val) ? (int) $val : 15;

        return $this
            ->hxNotify("Demasiados intentos. Intenta en {$minutes} minutos.", 'error')
            ->hxResponse(status: 200);
    }

    private function checkRateLimit(string $key): ?JsonResponse
    {
        if (! RateLimiter::tooManyAttempts($key, 10)) {
            RateLimiter::hit($key, 60);

            return null;
        }

        $val = RateLimiter::availableIn($key);
        $seconds = is_numeric($val) ? (int) $val : 0;

        return $this
            ->hxNotify("Demasiadas solicitudes. Espera {$seconds} segundos.", 'error')
            ->hxResponse(status: 200);
    }

    private function verifyRecaptcha(?string $token, string $ip): ?JsonResponse
    {
        if (! config('services.recaptcha.enabled', false)) {
            return null;
        }

        if (trim((string) ($token ?? '')) === '') {
            return $this
                ->hxNotify('Verifica que no eres un robot.', 'error')
                ->hxResponse(status: 200);
        }

        $secret = config('services.recaptcha.secret');
        $secret = is_scalar($secret) ? (string) $secret : '';
        $appUrl = config('app.url');
        $appUrl = is_scalar($appUrl) ? (string) $appUrl : '';
        $hostname = strval(parse_url($appUrl, PHP_URL_HOST) ?? '');

        $result = new ReCaptcha($secret)
            ->setExpectedHostname($hostname)
            ->verify($token, $ip);

        if (! $result->isSuccess()) {
            return $this
                ->hxNotify('Falló la verificación reCAPTCHA.', 'error')
                ->hxResponse(status: 200);
        }

        return null;
    }

    private function handleFailedAttempt(string $key, string $email, string $ip): JsonResponse
    {
        $maxVal = config('auth.login_max_attempts', 5);
        $max = is_numeric($maxVal) ? (int) $maxVal : 5;

        $decayVal = config('auth.login_decay_minutes', 15);
        $decay = is_numeric($decayVal) ? (int) $decayVal : 15;
        $attempts = (int) Cache::increment(self::ATTEMPTS_PREFIX.$key);

        Log::warning('Login fallido', [
            'email' => $this->maskEmail($email),
            'ip' => $ip,
            'attempts' => $attempts,
        ]);

        if ($attempts >= $max) {
            Cache::put(self::BLOCK_PREFIX.$key, true, now()->addMinutes($decay));

            Log::warning('Login bloqueado', [
                'email' => $this->maskEmail($email),
                'ip' => $ip,
            ]);

            return $this
                ->hxNotify("Bloqueado por {$decay} minutos por intentos excesivos.", 'error')
                ->hxResponse(status: 200);
        }

        $remaining = $max - $attempts;

        return $this
            ->hxNotify("Credenciales incorrectas. {$remaining} intentos restantes.", 'error')
            ->hxResponse(status: 200);
    }

    private function maskEmail(string $email): string
    {
        if (! str_contains($email, '@')) {
            return $email;
        }

        [$local, $domain] = explode('@', $email, 2);

        return mb_substr($local, 0, 2).'***@'.$domain;
    }
}
