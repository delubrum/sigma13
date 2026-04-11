<?php

declare(strict_types=1);

namespace App\Domain\Identity\Web\Actions\Password;

use App\Domain\Identity\Actions\InitiatePasswordReset;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class SendLink
{
    use AsAction;
    use HtmxOrchestrator;

    public function asController(Request $request): JsonResponse
    {
        $email = $request->input('email');

        if (! is_string($email) || blank($email)) {
            return $this->hxNotify('El correo es obligatorio.', 'error')->hxResponse();
        }

        InitiatePasswordReset::run($email);

        return $this
            ->hxNotify('Si el correo existe, recibirás el enlace en breve.', 'success')
            ->hxResponse();
    }
}
