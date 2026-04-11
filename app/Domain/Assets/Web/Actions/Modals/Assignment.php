<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Modals;

use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Models\AssetEvent;
use App\Domain\Users\Models\Employee;
use App\Domain\Shared\Data\Config;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Lorisleiva\Actions\Concerns\AsAction;

final class Assignment
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(Asset $asset, ?AssetEvent $event = null): Config
    {
        $employees = Employee::orderBy('name')->get();
        $employeeOptions = $employees->pluck('name', 'id')
            ->mapWithKeys(fn($name, $id) => [$id => "$id || " . mb_convert_case((string)$name, MB_CASE_TITLE, 'UTF-8')])
            ->toArray();

        return new Config(
            title: $event ? "Editar Asignación: <span class='opacity-50'>{$asset->serial}</span>" : "Asignar Activo: <span class='opacity-50'>{$asset->serial}</span>",
            subtitle: $asset->name ?? 'Detalle del activo',
            icon: 'ri-user-add-line',
            formFields: [
                new \App\Domain\Shared\Data\Field(
                    name: 'employee_id',
                    label: 'Responsable',
                    required: true,
                    options: $employeeOptions,
                    widget: 'slimselect'
                ),
                new \App\Domain\Shared\Data\Field(
                    name: 'hardware',
                    label: 'Inventario de Hardware',
                    widget: 'assets::components.widgets.hardware-list'
                ),
                new \App\Domain\Shared\Data\Field(
                    name: 'software',
                    label: 'Software Requerido',
                    widget: 'assets::components.widgets.software-list'
                ),
                new \App\Domain\Shared\Data\Field(
                    name: 'notes',
                    label: 'Observaciones',
                    type: 'textarea',
                    placeholder: 'Notas adicionales sobre la entrega...'
                ),
                new \App\Domain\Shared\Data\Field(
                    name: 'file',
                    label: 'Acta de Entrega (PDF)',
                    required: !$event,
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
                'software' => [],
            ],
            'customPostRoute' => route('assets.assignments.store', $asset->id),
            'closeEvent' => 'close-modal-2',
            'suffix' => '-2',
        ]);
    }

    public function asEdit(AssetEvent $event): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        Gate::authorize('140');

        return $this->hxView('components::new-modal', [
            'config' => $this->config($event->asset, $event),
            'data' => [
                'id' => $event->id,
                'employee_id' => $event->employee_id,
                'hardware' => $event->hardware ?? [],
                'software' => $event->software ?? [],
                'notes' => $event->notes,
            ],
            'customPostRoute' => route('assets.assignments.update', $event->id),
            'method' => 'patch',
            'closeEvent' => 'close-modal-2',
            'suffix' => '-2',
        ]);
    }

    public function asStore(Request $request, Asset $asset): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('140');
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'hardware' => 'nullable|array',
            'software' => 'nullable|array',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $event = AssetEvent::create([
            'kind' => 'assignment',
            'asset_id' => $asset->id,
            'employee_id' => $request->integer('employee_id'),
            'hardware' => $request->input('hardware', []),
            'software' => $request->input('software', []),
            'notes' => $request->input('notes'),
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        if ($request->hasFile('file')) {
            $event->addMediaFromRequest('file')
                ->toMediaCollection('minute');
        }

        $asset->update(['status' => 'assigned']);

        $this->hxNotify('Asignación registrada con éxito');
        return $this->commonResponse($asset->id);
    }

    public function asUpdate(Request $request, AssetEvent $event): \Illuminate\Http\JsonResponse
    {
        Gate::authorize('140');
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'hardware' => 'nullable|array',
            'software' => 'nullable|array',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $event->update([
            'employee_id' => $request->integer('employee_id'),
            'hardware' => $request->input('hardware', []),
            'software' => $request->input('software', []),
            'notes' => $request->input('notes'),
        ]);

        if ($request->hasFile('file')) {
            $event->clearMediaCollection('minute');
            $event->addMediaFromRequest('file')
                ->toMediaCollection('minute');
        }

        $this->hxNotify('Asignación actualizada con éxito');
        return $this->commonResponse($event->asset_id);
    }

    private function commonResponse(int $assetId): \Illuminate\Http\JsonResponse
    {
        $this->hxRefreshTables([
            'dt_assets',
            'dt_assets_movements_' . $assetId,
        ]);
        $this->hxRefresh([
            '#asset-status-badge',
            '#asset-assignee-card',
            '#asset-actions-area',
            '#tab-assets_movements_' . $assetId . '-container',
        ]);
        $this->hxTrigger('close-modal-2');

        return $this->hxResponse();
    }
}
