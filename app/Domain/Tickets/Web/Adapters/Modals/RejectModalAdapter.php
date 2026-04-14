<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Web\Adapters\Modals;

use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Domain\Tickets\Models\Ticket;
use App\Domain\Tickets\Models\TicketItem;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class RejectModalAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        $config = new Config(
            title: 'Rechazar Ticket',
            subtitle: "Indique la causa del rechazo para el Ticket #{$id}",
            icon: 'ri-close-circle-line',
            newButtonLabel: 'Confirmar Rechazo',
            modalWidth: '35',
            columns: [],
            formFields: [
                new Field(label: 'Causa del Rechazo', name: 'reason', type: 'textarea', required: true, placeholder: 'Describa por qué se rechaza este requerimiento...')
            ],
        );

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => $config->title,
            'subtitle' => $config->subtitle,
        ]);

        return response()->view('shared::components.new-modal', [
            'route'  => 'tickets.reject',
            'config' => $config,
            'data'   => ['id' => $id],
        ]);
    }

    public function upsert(Request $request): JsonResponse
    {
        $id = $request->integer('id');
        $reason = $request->string('reason')->toString();

        if (empty($reason)) {
            $this->hxNotify('Debe indicar una causa de rechazo.', 'error');
            return $this->hxResponse();
        }

        \App\Domain\Tickets\Actions\UpdateTicketStatusAction::run(
            id:     $id,
            status: 'Rejected',
            reason: $reason,
            userId: auth()->id()
        );

        $this->hxNotify('Ticket rechazado correctamente.');
        $this->hxRefreshTables(['dt_tickets']);
        $this->hxCloseModals(['modal-body', 'modal-body-2']);
        
        return $this->hxResponse();
    }
}
