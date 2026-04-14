<?php

declare(strict_types=1);

namespace App\Domain\Identity\Web\Adapters\Password;

use App\Domain\Identity\Actions\InitiatePasswordReset as SendTask;
use App\Domain\Identity\Data\LoginData;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class SendResetLinkAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $email): void
    {
        SendTask::run($email);
    }

    public function asController(Request $request): JsonResponse
    {
        $data = LoginData::from($request->all());

        $this->handle($data->email);

        return $this
            ->hxNotify('Si el correo existe, recibirás el enlace en breve.', 'success')
            ->hxResponse();
    }
}
