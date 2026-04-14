<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Web\Adapters\Modals;

use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Domain\Tickets\Actions\UpdateTicketStatusAction;
use App\Domain\Tickets\Models\Ticket;
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
            icon: 'ri-star-smile-line',
            subtitle: "Su feedback es muy importante para nosotros (Ticket #{$id})",
            newButtonLabel: 'Enviar Calificación',
            modalWidth: '30',
            columns: [],
            formFields: [
                new Field(
                    name: 'rating',
                    label: 'Calificación',
                    type: 'select',
                    required: true,
                    options: [
                        '5' => '5 - Excelente',
                        '4' => '4 - Bueno',
                        '3' => '3 - Regular',
                        '2' => '2 - Malo',
                        '1' => '1 - Muy Malo',
                    ]
                ),
                new Field(name: 'notes', label: 'Comentarios adicionales', type: 'textarea', placeholder: 'Cuéntanos un poco más...'),
            ],
        );

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => $config->title,
            'subtitle' => $config->subtitle,
        ]);

        return response()->view('shared::components.new-modal', [
            'route' => 'tickets.rate',
            'config' => $config,
            'data' => ['id' => $id],
        ]);
    }

    public function upsert(Request $request): JsonResponse
    {
        $id = $request->integer('id');
        $rating = $request->integer('rating');
        $notes = $request->string('notes')->toString();

        UpdateTicketStatusAction::run(
            id: $id,
            status: 'Rated',
            reason: "CALIFICADO ({$rating}/5): ".($notes ?: 'Sin comentarios adicionales'),
            userId: auth()->id()
        );

        // Actualizamos el rating en el modelo principal (específico de este flujo)
        Ticket::where('id', $id)->update(['rating' => $rating]);

        $this->hxNotify('Gracias por su calificación!');
        $this->hxRefreshTables(['dt_tickets']);
        $this->hxCloseModals(['modal-body', 'modal-body-2']);

        return $this->hxResponse();
    }
}
