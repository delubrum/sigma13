<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Web\Adapters\Modals;

use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Domain\Tickets\Actions\CreateActivityAction;
use App\Domain\Tickets\Data\ItemUpsertData;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

final class ItemModalAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        $config = new Config(
            title: 'New Activity',
            icon: 'ri-add-line',
            subtitle: "Registrar avance para el Ticket #{$id}",
            newButtonLabel: 'Guardar Avance',
            modalWidth: '40',
            columns: [],
            formFields: SchemaGenerator::toFields(ItemUpsertData::class),
        );

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => $config->title,
            'subtitle' => $config->subtitle,
        ]);

        return response()->view('shared::components.new-modal', [
            'route' => 'tickets.item',
            'config' => $config,
            'data' => ['id' => $id],
        ]);
    }

    public function upsert(Request $request): JsonResponse
    {
        try {
            $data = ItemUpsertData::validateAndCreate($request->all());
        } catch (ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            $errorMsg = is_string($firstError) ? $firstError : 'Validación fallida';
            $this->hxNotify('Error: '.$errorMsg, 'error');

            return $this->hxResponse(['errors' => $e->errors()], 422);
        }

        CreateActivityAction::run(
            ticketId: $data->id,
            data: $data,
            currentUserId: auth()->id()
        );

        $this->hxNotify('Actividad registrada correctamente');
        $this->hxRefresh(['#tab-content', '#sidebar-summary']);
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }
}
