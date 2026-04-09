<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Upsert as UpsertData;
use App\Domain\Assets\Models\Asset;
use App\Domain\Shared\Data\Config;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Upsert
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(?int $id = null): Config
    {
        return new Config(
            title: $id ? 'Editar Activo' : 'Nuevo Activo',
            subtitle: $id ? "Editando activo ID #{$id}" : 'Registro de nuevo activo tecnológico',
            icon: 'ri-stack-line',
            newButtonLabel: 'Guardar Activo',
            modalWidth: '90',
            formFields: UpsertData::fields(),
        );
    }

    public function asController(Request $request, ?int $id = null): Response
    {
        $asset = $id ? Asset::findOrFail($id) : null;

        // Magia de Spatie Data: Auto-mapeo desde el modelo
        $data = $asset ? UpsertData::from($asset) : [];
        $config = $this->config($id);
        $this->hxModalWidth($config->modalWidth);

        $this->hxTriggers['open-modal'] = [];

        return $this->hxView(view('components.new-modal', [
            'route' => 'assets',
            'config' => $config,
            'data' => $data,
        ]));
    }

    public function handle(UpsertData $data): JsonResponse
    {
        Asset::updateOrCreate(['id' => $data->id], $data->toArray());

        $this->hxNotify($data->id ? 'Activo actualizado correctamente' : 'Activo creado correctamente');
        $this->hxRefreshTables(['dt_assets']);
        $this->hxCloseModals(['modal-body']);

        return $this->hxResponse();
    }
}
