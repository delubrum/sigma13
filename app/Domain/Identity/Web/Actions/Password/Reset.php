<?php

declare(strict_types=1);

namespace App\Domain\Identity\Web\Actions\Password;

use App\Domain\Identity\Data\ResetData;
use App\Domain\Users\Models\User;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

final class Reset
{
    use AsAction;
    use HtmxOrchestrator;

    public function asController(Request $request): JsonResponse
    {
        try {
            // 1. EL MURO: Esto valida 'password' vs 'password_confirmation'
            // y las reglas de longitud/caracteres.
            // Si falla, NO toca la base de datos (el token sigue vivo).
            $data = ResetData::validateAndCreate($request->all());

        } catch (ValidationException $e) {
            // 2. EXTRACCIÓN: Obtenemos el primer error específico:
            // "La confirmación de contraseña no coincide" o "El campo es requerido"
            $errors = $e->errors();
            $firstError = collect($errors)->flatten()->first();
            $error = is_scalar($firstError) ? (string) $firstError : '';

            return $this
                ->hxNotify($error, 'error')
                ->hxResponse(); // Enviamos 200 para que HTMX procese el header del Toast
        }

        // 3. EL PUNTO DE NO RETORNO: Solo llegamos aquí si los datos son perfectos.
        // Aquí es donde el broker de Laravel consume (quema) el token.
        $status = $this->handle($data);

        if ($status !== Password::PASSWORD_RESET) {
            return $this
                ->hxNotify('El enlace es inválido o ya fue utilizado.', 'error')
                ->hxResponse();
        }

        return $this
            ->hxNotify('Contraseña actualizada correctamente.', 'success')
            ->hxRedirect(route('login'));
    }

    public function handle(ResetData $data): string
    {
        $status = Password::reset(
            credentials: [
                'email' => $data->email,
                'token' => $data->token,
                'password' => $data->password,
                'password_confirmation' => $data->password_confirmation,
            ],
            callback: function ($user, $password): void {
                /** @var User $user */
                $user->forceFill(['password' => Hash::make($password)])->save();
            },
        );

        return is_scalar($status) ? (string) $status : '';
    }
}
