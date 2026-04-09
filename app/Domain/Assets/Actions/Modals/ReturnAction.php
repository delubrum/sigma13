<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions\Modals;

use App\Domain\Assets\Data\Modals\ReturnAsset;
use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Models\AssetEvent;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
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

    public function config(): Config
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
                    type: 'textarea',
                    required: false,
                    placeholder: 'Motivo de la devolución o condición del equipo...',
                ),
            ],
        );
    }

    public function handle(int $id): View
    {
        $config = $this->config();

        $this->hxModalWidth($config->modalWidth, '-2');
        $this->hxTriggers['open-modal-2'] = true;

        return view('components.new-modal', [
            'route' => "assets/{$id}/return",
            'config' => $config,
            'target' => '#modal-body-2',
            'closeEvent' => 'close-modal-2',
            'suffix' => '-2',
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
