<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Web\Adapters;

use App\Domain\Tickets\Models\Ticket;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class CloseAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id, Request $request): JsonResponse
    {
        $ticket = Ticket::withCount('items')->findOrFail($id);

        if ($ticket->items_count === 0) {
            $this->hxNotify('Error: Debes registrar al menos un avance antes de cerrar.', 'error');
            return $this->hxResponse();
        }

        \App\Domain\Tickets\Actions\UpdateTicketStatusAction::run(
            id:     $id,
            status: 'Closed',
            userId: auth()->id()
        );

        $this->hxNotify('Ticket cerrado correctamente.');
        $this->hxRefreshTables(['dt_tickets']);
        $this->hxCloseModals(['modal-body']);
        
        return $this->hxResponse();
    }
}
