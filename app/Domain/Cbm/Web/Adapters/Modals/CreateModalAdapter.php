<?php

declare(strict_types=1);

namespace App\Domain\Cbm\Web\Adapters\Modals;

use App\Domain\Cbm\Actions\SaveCbmAction;
use App\Domain\Cbm\Data\UpsertData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

final class CreateModalAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        $config = new Config(
            title: 'Nuevo Proyecto CBM',
            icon: 'ri-add-line',
            subtitle: 'Suba el archivo Excel con las dimensiones para calcular la carga óptima',
            newButtonLabel: 'Procesar Carga',
            modalWidth: '40',
            columns: [],
            formFields: SchemaGenerator::toFields(UpsertData::class),
        );

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => $config->title,
            'subtitle' => $config->subtitle,
        ]);

        return response()->view('shared::components.new-modal', [
            'route' => 'cbm',
            'config' => $config,
        ]);
    }

    public function save(Request $request): JsonResponse
    {
        try {
            $data = UpsertData::validateAndCreate($request->all());
        } catch (ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            $errorMsg = is_string($firstError) ? $firstError : 'Validación fallida';
            $this->hxNotify('Error: '.$errorMsg, 'error');

            return $this->hxResponse(['errors' => $e->errors()], 422);
        }

        SaveCbmAction::run($data, (int) auth()->id());

        $this->hxNotify('Proyecto CBM creado y procesado correctamente');
        $this->hxRefreshTables(['dt_cbm']);
        $this->hxCloseModals(['modal-body']);

        return $this->hxResponse();
    }
}
