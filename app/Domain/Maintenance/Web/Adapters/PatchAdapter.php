<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Web\Adapters;

use App\Domain\Maintenance\Models\Maintenance;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class PatchAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id, Request $request): JsonResponse
    {
        $mnt = Maintenance::findOrFail($id);
        $field = $request->string('field')->toString();
        $value = $request->input($field);

        $allowedFields = [
            'priority', 'status', 'facility', 'asset_id', 
            'assignee_id', 'sgc', 'root_cause', 'rating'
        ];

        if (! in_array($field, $allowedFields, true)) {
            abort(403, 'Campo no permitido.');
        }

        $mnt->update([$field => $value]);

        $this->hxNotify('Registro actualizado correctamente');
        $this->hxRefreshTables(['dt_maintenance']);
        $this->hxRefresh(['#sidebar-summary', '#head-status']); // Refresh sidebar and status if visible

        return $this->hxResponse();
    }
}
