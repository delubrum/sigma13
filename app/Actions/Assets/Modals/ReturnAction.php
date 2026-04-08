<?php

declare(strict_types=1);

namespace App\Actions\Assets\Modals;

use App\Data\Assets\Modals\ReturnAsset;
use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Models\Asset;
use App\Models\AssetEvent;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

final class ReturnAction
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(int $id): Config
    {
        return new Config(
            title: 'Devolver Activo',
            subtitle: 'Registro de devolución al área de IT',
            icon: 'ri-arrow-go-back-line',
            newButtonLabel: 'Registrar Devolución',
            modalWidth: '40%',
            columns: [],
            formFields: [
                new Field(
                    name: 'notes',
                    label: 'Observaciones / Motivo',
                    required: false,
                    placeholder: 'Motivo de la devolución o condición del equipo...',
                    type: 'textarea',
                ),
            ],
        );
    }

    public function handle(int $id): View
    {
        $config = $this->config($id);

        $this->hxModalHeader([
            'icon' => $config->icon,
            'title' => $config->title,
            'subtitle' => $config->subtitle,
        ], '-2');

        $this->hxModalWidth($config->modalWidth, '-2');
        $this->hxTriggers['open-modal-2'] = true;

        return view('components.new-modal', [
            'route' => "assets/{$id}/returns",
            'config' => $config,
            'target' => '#modal-body-2',
            'closeEvent' => 'close-modal-2',
        ]);
    }

    public function asController(Request $request, int $id): Response
    {
        return $this->hxView($this->handle($id));
    }

    public function asStore(Request $request, int $id): JsonResponse
    {
        $data = ReturnAsset::from($request->all());

        // Find the latest assignment to inherit the employee_id
        $lastAssignment = AssetEvent::where('asset_id', $id)
            ->where('kind', 'assignment')
            ->orderByDesc('id')
            ->first();

        AssetEvent::create([
            'kind' => 'return',
            'asset_id' => $id,
            'employee_id' => $lastAssignment?->employee_id,
            'notes' => $data->notes,
            'user_id' => Auth::id(),
            // Hardware and software could be inherited, but technically they are returned.
            'hardware' => $lastAssignment?->hardware,
            'software' => $lastAssignment?->software,
        ]);

        Asset::where('id', $id)->update(['status' => 'available']);

        $this->hxNotify('Devolución registrada correctamente');
        $this->hxRefreshTables(['tabTableMovements']);
        $this->hxRefresh(['sidebar-summary']);
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }
}
