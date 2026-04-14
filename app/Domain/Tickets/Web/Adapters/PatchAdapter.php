<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Web\Adapters;

use App\Domain\Tickets\Models\Ticket;
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
        $ticket = Ticket::findOrFail($id);
        $field = $request->string('field')->toString();
        $value = $request->input($field);

        if (! in_array($field, ['priority', 'status', 'facility', 'kind', 'asset_id', 'assignee_id', 'sgc', 'root_cause'], true)) {
            abort(403, 'Campo no permitido.');
        }

        $ticket->update([$field => $value]);

        $this->hxNotify('Ticket actualizado correctamente');
        $this->hxRefreshTables(['dt_tickets']);

        return $this->hxResponse();
    }
}
