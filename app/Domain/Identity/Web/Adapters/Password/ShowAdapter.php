<?php

declare(strict_types=1);

namespace App\Domain\Identity\Web\Adapters\Password;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;
use Lorisleiva\Actions\Concerns\AsAction;

final class ShowAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $token, Request $request): Response
    {
        // Nota: El modelo se consulta aquí para validación técnica del framework,
        // pero se evita inyectar lógica de negocio compleja.
        /** @var class-string<\Illuminate\Database\Eloquent\Model> $userModel */
        $userModel = config('auth.providers.users.model');
        $user = $userModel::where('email', $request->email)->first();

        if (! $user || ! Password::getRepository()->exists($user, $token)) {
            return $this->hxView('auth::error-token', [
                'title' => 'Enlace Inválido',
                'message' => 'El enlace de recuperación ha expirado o ya fue utilizado.',
            ]);
        }

        return $this->hxView('auth::reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }
}
