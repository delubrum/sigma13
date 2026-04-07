<?php

declare(strict_types=1);

namespace App\Actions\Password;

use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Lorisleiva\Actions\Concerns\AsAction;

final class Show
{
    use AsAction;

    public function handle(string $token, Request $request): Factory|View
    {
        $user = User::where('email', $request->email)->first();

        // Si falla la validación del token en Valkey/DB
        if (! $user || ! Password::getRepository()->exists($user, $token)) {
            return view('auth.error-token', [
                'title' => 'Enlace Inválido',
                'message' => 'El enlace de recuperación ha expirado o ya fue utilizado.',
            ]);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }
}
