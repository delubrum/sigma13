<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions\Modals;

use App\Domain\Assets\Data\Modals\AutomationData;
use App\Domain\Maintenance\Models\MntPreventiveForm;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Automation
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        return new Config(
            title: 'Nueva Automatización',
            subtitle: 'Configura una tarea para el cronograma maestro',
            icon: 'ri-settings-4-line',
            newButtonLabel: 'Guardar Regla',
            modalWidth: '40%',
            columns: [],
            formFields: [
                new Field(
                    name: 'activity',
                    label: 'Descripción de la Tarea',
                    type: 'text',
                    required: true,
                    placeholder: 'Limpieza profunda, Revisión, Chequeo...',
                ),
                new Field(
                    name: 'frequency',
                    label: 'Frecuencia (Periodo)',
                    type: 'select',
                    required: true,
                    placeholder: '',
                    options: [
                        'weekly' => 'Semanal',
                        'monthly' => 'Mensual',
                        'quarterly' => 'Trimestral',
                        'semiannual' => 'Semestral',
                        'annual' => 'Anual',
                        'annualx2' => 'Cada 2 Años',
                        'annualx3' => 'Cada 3 Años',
                        'annualx5' => 'Cada 5 Años',
                    ]
                ),
                new Field(
                    name: 'last_performed_at',
                    label: 'Última Fecha de Realización',
                    type: 'date',
                    required: false,
                    placeholder: 'YYYY-MM-DD (Opcional)',
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
            'route' => "assets/{$id}/automation",
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
        $data = AutomationData::from($request->all());

        MntPreventiveForm::create([
            'asset_id' => $id,
            'kind' => 'task',
            'activity' => $data->activity,
            'frequency' => $data->frequency,
            'last_performed_at' => $data->last_performed_at,
        ]);

        $this->hxNotify('Automatización agregada al cronograma maestro.');
        $this->hxRefreshTables(['tabTableAutomations']);
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }
}
