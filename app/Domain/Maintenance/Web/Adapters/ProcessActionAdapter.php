<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Web\Adapters;

use App\Domain\Maintenance\Models\Maintenance;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class ProcessActionAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id, string $action): JsonResponse
    {
        $mnt = Maintenance::findOrFail($id);
        
        match ($action) {
            'attend' => $mnt->update([
                'status' => 'Started',
                'started_at' => now(),
            ]),
            'finish' => $mnt->update([
                'status' => 'Attended',
                'ended_at' => now(),
            ]),
            'close' => $mnt->update([
                'status' => 'Closed',
                'closed_at' => now(),
            ]),
            'reject' => $mnt->update([
                'status' => 'Rejected',
            ]),
            default => abort(400, 'Acción no válida'),
        };

        $this->hxNotify("Ticket ".ucfirst($action)." correctamente");
        $this->hxRefreshTables(['dt_maintenance']);
        $this->hxRefresh(['#sidebar-summary', '#tab-content']);

        return $this->hxResponse();
    }

    public function asController(int $id, string $action): JsonResponse
    {
        return $this->handle($id, $action);
    }
}
