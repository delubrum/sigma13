<?php

declare(strict_types=1);

namespace App\Domain\Identity\Web\Adapters\Password;

use App\Domain\Identity\Actions\ResetPassword as ResetPasswordCore;
use App\Domain\Identity\Data\ResetData;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

final class ResetAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    /** GET /password/reset/{token} */
    public function handle(string $token, Request $request): Response
    {
        return $this->hxView('auth::reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /** POST /password/reset */
    public function asController(Request $request): JsonResponse
    {
        try {
            $data = ResetData::validateAndCreate($request->all());
        } catch (ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();

            return $this->hxNotify(is_string($firstError) ? $firstError : 'Error de validación', 'error')->hxResponse();
        }

        // DELEGACIÓN: Llamamos a la Core Action
        $status = ResetPasswordCore::run($data);

        if ($status !== Password::PASSWORD_RESET) {
            return $this
                ->hxNotify('El enlace es inválido o ya fue utilizado.', 'error')
                ->hxResponse();
        }

        return $this
            ->hxNotify('Contraseña actualizada correctamente.', 'success')
            ->hxRedirect(route('login'));
    }
}
