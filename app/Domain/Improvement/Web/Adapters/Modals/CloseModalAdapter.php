<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Web\Adapters\Modals;

use App\Domain\Improvement\Models\Improvement;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class CloseModalAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'cdate' => ['required', 'date'],
            'convenience' => ['required', 'string'],
            'adequacy' => ['required', 'string'],
            'effectiveness' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        Improvement::findOrFail($id)->update([
            'cdate' => $data['cdate'],
            'convenience' => $data['convenience'],
            'adequacy' => $data['adequacy'],
            'effectiveness' => $data['effectiveness'],
            'notes' => $data['notes'] ?? null,
            'status' => 'Closed',
            'closed_at' => now(),
        ]);

        $this->hxNotify('Mejora cerrada correctamente.');
        $this->hxRefreshTables(['dt_improvement']);
        $this->hxCloseModals(['modal-body', 'modal-body-2']);

        return $this->hxResponse();
    }

    public function asController(int $id, Request $request): JsonResponse
    {
        return $this->handle($id, $request);
    }
}
