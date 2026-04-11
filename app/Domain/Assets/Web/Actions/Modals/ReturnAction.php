<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Modals;

use App\Domain\Assets\Actions\RegisterReturn;
use App\Domain\Assets\Data\Modals\ReturnData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

final class ReturnAction
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        return new Config(
            title: 'Registrar Devolución',
            subtitle: 'Verificación física y liberación de activo',
            icon: 'ri-arrow-left-down-line',
            modalWidth: '50',
            formFields: SchemaGenerator::toFields(ReturnData::class),
        );
    }

    public function handle(int $id): View
    {
        $config = $this->config();
        $this->hxModalWidth($config->modalWidth, '-2');
        $this->hxTriggers['open-modal-2'] = true;

        return view('components.new-modal', [
            'route'      => "assets/{$id}/returns",
            'config'     => $config,
            'target'     => '#modal-body-2',
            'closeEvent' => 'close-modal-2',
            'suffix'     => '-2',
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->hxView($this->handle($id));
    }

    public function asStore(Request $request, int $id): JsonResponse|Response
    {
        try {
            $data = ReturnData::from($request->all());

            RegisterReturn::run($id, $data, (int) Auth::id());

            $this->hxNotify('Devolución procesada y activo liberado');
            $this->hxRefreshTables(['tabTableMovements']);
            $this->hxRefresh(['sidebar-summary']);
            $this->hxCloseModals(['modal-body-2']);

            return $this->hxResponse();

        } catch (ValidationException $e) {
            return response()
                ->view('components.new-modal', [
                    'route'   => "assets/{$id}/returns",
                    'config'  => $this->config(),
                    'errors'  => $e->validator->errors(),
                    'assetId' => $id,
                    'suffix'  => '-2',
                ], 422)
                ->header('HX-Retarget', '#modal-body-2');
        }
    }
}