<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Modals;

use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Data\Modals\AutomationData;
use App\Domain\Maintenance\Models\MntPreventiveForm;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Honeypot\ProtectAgainstSpam;

final class Automation
{
    use AsAction;

    /** @return list<class-string> */
    /** @return list<string> */
    public function getControllerMiddleware(): array
    {
        return [ProtectAgainstSpam::class];
    }

    use HtmxOrchestrator;

    public function config(?Asset $asset = null): Config
    {
        return new Config(
            title: $asset ? "Nueva Automatización: <span class='opacity-50'>{$asset->serial}</span>" : 'Nueva Automatización',
            subtitle: $asset?->name ?? 'Configura una tarea para el cronograma maestro',
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

    public function handle(Asset $asset): Response
    {
        $config = $this->config($asset);

        return $this->hxView('components::new-modal', [
            'route' => "assets/{$asset->id}/automation",
            'config' => $config,
            'target' => '#modal-body-2',
            'closeEvent' => 'close-modal-2',
            'suffix' => '-2',
        ]);
    }

    public function asController(Asset $asset): Response
    {
        return $this->handle($asset);
    }

    public function asStore(Request $request, Asset $asset): JsonResponse
    {
        $data = AutomationData::from($request->all());

        MntPreventiveForm::create([
            'asset_id' => $asset->id,
            'kind' => 'task',
            'activity' => $data->activity,
            'frequency' => $data->frequency,
            'last_performed_at' => $data->last_performed_at,
        ]);

        $this->hxNotify('Automatización agregada al cronograma maestro.');
        $this->hxRefreshTables(['dt_assets', 'dt_assets_automations_' . $asset->id]);
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }
}
