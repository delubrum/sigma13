<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\NotificationService;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    use HtmxOrchestrator;

    public function __construct(
        protected NotificationService $notifications
    ) {}

    public function sendResetLink(Request $request): JsonResponse
    {
        $validated = $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user) {
            return $this->hxNotify('No pudimos encontrar un usuario con ese correo electrónico.', 'error')
                ->hxResponse(['message' => 'No pudimos encontrar un usuario con ese correo electrónico.'], 422);
        }

        $token = Password::createToken($user);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $validated['email']],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $this->notifications->passwordReset($validated['email'], $token);

        return $this->hxNotify('Enlace enviado correctamente', 'success')
            ->hxResponse(['message' => 'Enlace enviado correctamente']);
    }

    public function reset(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (! $record || ! Hash::check($validated['token'], (string) $record->token)) {
            return $this->hxNotify('El token de restablecimiento de contraseña es inválido.', 'error')
                ->hxResponse(['message' => 'El token de restablecimiento de contraseña es inválido.'], 422);
        }

        $user = User::where('email', $validated['email'])->first();
        if (! $user) {
            return $this->hxNotify('Usuario no encontrado.', 'error')
                ->hxResponse(['message' => 'Usuario no encontrado.'], 422);
        }

        $user->forceFill(['password' => bcrypt($validated['password'])])->save();

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        $this->notifications->telegram("🔓 <b>Contraseña actualizada</b>\n\n▸ Email: {$validated['email']}\n▸ Hora: ".now()->format('H:i'));

        if ($request->ajax() || $request->header('HX-Request')) {
            return $this->hxNotify('Contraseña actualizada correctamente', 'success')
                ->hxRedirect('/login');
        }

        return to_route('login')->with('status', 'Contraseña actualizada correctamente');
    }
}
