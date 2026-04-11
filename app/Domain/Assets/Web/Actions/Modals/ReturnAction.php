<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Modals;

use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Models\AssetEvent;
use App\Domain\Shared\Data\Config;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

final class ReturnAction
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(?Asset $asset = null): Config
    {
        return new Config(
            title: $asset ? "Retornar Activo: <span class='opacity-50'>{$asset->serial}</span>" : 'Retornar Activo',
            subtitle: $asset?->name ?? 'Registrar la devolución física de un activo',
            icon: 'ri-reply-line',
            formFields: [
                new \App\Domain\Shared\Data\Field(
                    name: 'hardware',
                    label: 'Estado del Hardware',
                    widget: 'assets::components.widgets.condition-grid'
                ),
                new \App\Domain\Shared\Data\Field(
                    name: 'wipe',
                    label: 'Borrado Seguro de Datos',
                    required: true,
                    type: 'select',
                    options: ['Yes' => 'Sí, realizado', 'No' => 'No realizado', 'N/A' => 'No aplica']
                ),
                new \App\Domain\Shared\Data\Field(
                    name: 'notes',
                    label: 'Estado General / Observaciones',
                    type: 'textarea',
                    placeholder: 'Describa cualquier detalle adicional sobre el retorno...'
                ),
                new \App\Domain\Shared\Data\Field(
                    name: 'file',
                    label: 'Acta de Retorno (PDF)',
                    required: true,
                    widget: 'sigma-file',
                    accept: 'application/pdf'
                ),
            ],
            modalWidth: 'lg',
            multipart: true
        );
    }

    public function asController(Asset $asset): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        Gate::authorize('140');
        
        return $this->hxView('components::new-modal', [
            'config' => $this->config($asset),
            'data' => [
                'hardware' => [],
            ],
            'customPostRoute' => route('assets.returns.store', $asset->id),
            'closeEvent' => 'close-modal-2',
            'suffix' => '-2',
        ]);
    }

    public function asStore(Request $request, Asset $asset): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('140');
        $request->validate([
            'hardware' => 'required|array',
            'notes' => 'nullable|string',
            'wipe' => 'nullable|string',
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        // 1. Create Event
        $event = AssetEvent::create([
            'kind' => 'return',
            'asset_id' => $asset->id,
            'employee_id' => $asset->currentAssignment?->employee_id, // Who is returning it?
            'hardware' => $request->input('hardware', []),
            'software' => [], // Returns usually don't track software changes
            'notes' => $request->input('notes'),
            'wipe' => $request->input('wipe') === 'Yes',
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        // 2. Handle File (Minute)
        $event->addMediaFromRequest('file')
            ->toMediaCollection('minute');

        // 3. Update Asset
        $asset->update(['status' => 'available']);

        // 4. Response with Triggers
        $this->hxNotify('Activo devuelto con éxito');
        $this->hxRefreshTables([
            'dt_assets',
            'dt_assets_movements_' . $asset->id,
        ]);
        $this->hxRefresh([
            '#asset-status-badge',     
            '#asset-assignee-card',    
            '#asset-actions-area',
            '#tab-assets_movements_' . $asset->id . '-container',
        ]);
        $this->hxTrigger('close-modal-2');

        return $this->hxResponse();
    }
}
