<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Web\Adapters;

use App\Domain\Improvement\Models\Improvement;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class RejectAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id, Request $request): JsonResponse
    {
        $reason = $request->string('prompt_value')->toString();

        if (blank($reason)) {
            $this->hxNotify('Debe indicar un motivo de rechazo.', 'error');

            return $this->hxResponse();
        }

        Improvement::findOrFail($id)->update([
            'status'           => 'Rejected',
            'rejection_reason' => $reason,
        ]);

        $this->hxNotify('Mejora rechazada.');
        $this->hxRefreshTables(['dt_improvement']);
        $this->hxCloseModals(['modal-body']);

        return $this->hxResponse();
    }

    public function asController(int $id, Request $request): JsonResponse
    {
        return $this->handle($id, $request);
    }
}
