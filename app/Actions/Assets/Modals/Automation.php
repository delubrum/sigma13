<?php

declare(strict_types=1);

namespace App\Actions\Assets\Modals;

use App\Data\Assets\Modals\AutomationData;
use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Models\MntPreventiveForm;
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

    public function config(int $id): Config
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
                    required: true,
                    placeholder: 'Limpieza profunda, Revisión, Chequeo...',
                    type: 'text',
                ),
                new Field(
                    name: 'frequency',
                    label: 'Frecuencia (Periodo)',
                    required: true,
                    placeholder: '',
                    type: 'select',
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
                    required: false,
                    placeholder: 'YYYY-MM-DD (Opcional)',
                    type: 'date',
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

        return view('components.new-modal', [
            'route' => "assets/{$id}/automations",
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
