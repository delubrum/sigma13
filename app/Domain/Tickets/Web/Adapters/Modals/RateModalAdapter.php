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

final class RateModalAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        $config = new Config(
            title: 'Calificar Servicio',
            subtitle: "Su feedback es muy importante para nosotros (Ticket #{$id})",
            icon: 'ri-star-smile-line',
            newButtonLabel: 'Enviar Calificación',
            modalWidth: '30',
            columns: [],
            formFields: [
                new Field(
                    label: 'Calificación', 
                    name: 'rating', 
                    type: 'select', 
                    options: [
                        '5' => '5 - Excelente', 
                        '4' => '4 - Bueno', 
                        '3' => '3 - Regular', 
                        '2' => '2 - Malo', 
                        '1' => '1 - Muy Malo'
                    ], 
                    required: true
                ),
                new Field(label: 'Comentarios adicionales', name: 'notes', type: 'textarea', placeholder: 'Cuéntanos un poco más...')
            ],
        );

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => $config->title,
            'subtitle' => $config->subtitle,
        ]);

        return response()->view('shared::components.new-modal', [
            'route'  => 'tickets.rate',
            'config' => $config,
            'data'   => ['id' => $id],
        ]);
    }

    public function upsert(Request $request): JsonResponse
    {
        $id = $request->integer('id');
        $rating = $request->integer('rating');
        $notes = $request->string('notes')->toString();

        \App\Domain\Tickets\Actions\UpdateTicketStatusAction::run(
            id:     $id,
            status: 'Rated',
            reason: "CALIFICADO ({$rating}/5): " . ($notes ?: 'Sin comentarios adicionales'),
            userId: auth()->id()
        );

        // Actualizamos el rating en el modelo principal (específico de este flujo)
        \App\Domain\Tickets\Models\Ticket::where('id', $id)->update(['rating' => $rating]);

        $this->hxNotify('Gracias por su calificación!');
        $this->hxRefreshTables(['dt_tickets']);
        $this->hxCloseModals(['modal-body', 'modal-body-2']);
        
        return $this->hxResponse();
    }
}
