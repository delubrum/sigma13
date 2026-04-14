<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters\Modals;

use App\Domain\Assets\Actions\RegisterAutomationAction;
use App\Domain\Assets\Data\Modals\AutomationModalData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class AutomationsModalAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        return new Config(
            title:      'Programar Automatización',
            subtitle:   'Configuración de tareas de mantenimiento preventivo',
            icon:       'ri-settings-4-line',
            modalWidth: '50',
            formFields: SchemaGenerator::toFields(AutomationModalData::class),
        );
    }

    public function handle(int $id): View
    {
        $config = $this->config();
        $this->hxModalWidth($config->modalWidth, '-2');
        $this->hxTriggers['open-modal-2'] = true;

        return view('components.new-modal', [
            'route'      => "assets/{$id}/automations",
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
        $data = AutomationModalData::from($request->all());

        RegisterAutomationAction::run($id, $data);

        $this->hxNotify('Automatización programada correctamente');
        $this->hxRefreshTables(['tabTableAutomations']);
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }
}
