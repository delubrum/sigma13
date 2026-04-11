<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Modals;

use App\Domain\Assets\Actions\RegisterReturn;
use App\Domain\Assets\Data\Modals\ReturnData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class ReturnAction
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        return new Config(
            title: 'Registrar Devolución',
            subtitle: 'El activo volverá a estar disponible',
            icon: 'ri-arrow-go-back-line',
            newButtonLabel: 'Devolución',
            modalWidth: '40%',
            columns: [],
            formFields: [
                new Field(name: 'notes', label: 'Notas de devolución', type: 'textarea', required: false),
            ],
        );
    }

    public function handle(int $id): View
    {
        return view('components.new-modal', [
            'route' => "assets/{$id}/returns",
            'config' => $this->config(),
            'target' => '#modal-body-2',
            'suffix' => '-2',
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->hxView($this->handle($id));
    }

    public function asStore(Request $request, int $id): JsonResponse
    {
        $data = ReturnData::from($request->all());
        
        RegisterReturn::run($id, $data);

        $this->hxNotify('Activo devuelto correctamente');
        $this->hxRefreshTables(['tabTableMovements']);
        $this->hxRefresh(['sidebar-summary']);
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }
}
