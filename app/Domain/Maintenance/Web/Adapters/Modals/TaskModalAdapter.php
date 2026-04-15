<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Web\Adapters\Modals;

use App\Domain\Maintenance\Actions\CreateMaintenanceTaskAction;
use App\Domain\Maintenance\Data\TaskUpsertData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

final class TaskModalAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(int $id): Response
    {
        $config = new Config(
            title: 'Nueva Tarea',
            icon: 'ri-add-line',
            subtitle: "Registrar progreso para el Ticket #{$id}",
            newButtonLabel: 'Guardar Tarea',
            modalWidth: '40',
            columns: [],
            formFields: SchemaGenerator::toFields(TaskUpsertData::class),
        );

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => $config->title,
            'subtitle' => $config->subtitle,
        ]);

        return response()->view('shared::components.new-modal', [
            'route' => 'maintenance.tasks', // Base route
            'config' => $config,
            'data' => ['mnt_id' => $id], // Map to TaskUpsertData property
        ]);
    }

    public function upsert(Request $request): JsonResponse
    {
        try {
            $data = TaskUpsertData::validateAndCreate($request->all());
        } catch (ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            $errorMsg = is_string($firstError) ? $firstError : 'Validación fallida';
            $this->hxNotify('Error: '.$errorMsg, 'error');

            return $this->hxResponse(['errors' => $e->errors()], 422);
        }

        CreateMaintenanceTaskAction::run(
            data: $data,
            userId: (int) auth()->id()
        );

        $this->hxNotify('Tarea registrada correctamente');
        // Refresh the tasks subtable. The table ID in tab-index is dt_{jsFriendlyName}
        // jsFriendlyName = str_replace(...) . '_' . $parentId
        $this->hxRefresh(['body']); // Simplest for now, or use specific target
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }
}
