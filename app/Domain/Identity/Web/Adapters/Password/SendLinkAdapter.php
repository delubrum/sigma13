<?php

declare(strict_types=1);

namespace App\Domain\Identity\Web\Adapters\Password;

use App\Domain\Identity\Actions\InitiatePasswordReset;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class SendLinkAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $email): JsonResponse
    {
        if (blank($email)) {
            return $this->hxNotify('El correo es obligatorio.', 'error')->hxResponse();
        }

        InitiatePasswordReset::run($email);

        return $this
            ->hxNotify('Si el correo existe, recibirás el enlace en breve.', 'success')
            ->hxResponse();
    }

    public function asController(Request $request): JsonResponse
    {
        return $this->handle((string) $request->input('email', ''));
    }
}
