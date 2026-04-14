<?php

declare(strict_types=1);

namespace App\Domain\Printing\Web\Adapters;

use App\Domain\Printing\Models\Wo;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

final class DeleteAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(Wo $wo): JsonResponse
    {
        $code = $wo->code;

        $wo->items()->delete();
        $wo->delete();

        Storage::disk('public')->deleteDirectory("print/{$code}");

        $this->hxNotify("WO {$code} eliminada");
        $this->hxRefreshTables(['dt_printing']);
        $this->hxCloseModals(['modal-body']);

        return $this->hxResponse();
    }

    public function asController(Wo $wo): JsonResponse
    {
        return $this->handle($wo);
    }
}
