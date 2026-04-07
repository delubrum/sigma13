<?php

declare(strict_types=1);

namespace App\Actions\Assets\Modals;

use App\Data\Assets\Modals\Assignment;
use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Models\Asset;
use App\Models\AssetEvent;
use App\Models\Employee;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

final class CreateAssignment
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(int $id): Config
    {
        $employees = once(function (): array {
            return Employee::orderBy('name')->get()
                ->mapWithKeys(fn ($e) => [(string) $e->id => "{$e->id} || {$e->name}"])
                ->all();
        });

        return new Config(
            title: 'Nueva Asignación',
            subtitle: 'Registro de asignación de activo',
            icon: 'ri-user-add-line',
            newButtonLabel: 'Nueva Asignación',
            modalWidth: '50%',
            columns: [],
            formFields: [
                new Field(
                    name: 'employee_id',
                    label: 'Responsable',
                    required: true,
                    placeholder: 'Seleccionar empleado',
                    type: 'select',
                    options: $employees,
                ),
                new Field(
                    name: 'hardware',
                    label: 'Hardware',
                    required: false,
                    placeholder: 'Base, Teclado, Mouse, etc. (separado por comas)',
                    type: 'text',
                    hint: 'Separar múltiples items con comas',
                ),
                new Field(
                    name: 'software',
                    label: 'Software',
                    required: false,
                    placeholder: 'Office 365, Autodesk, etc. (separado por comas)',
                    type: 'text',
                    hint: 'Separar múltiples items con comas',
                ),
                new Field(
                    name: 'notes',
                    label: 'Acta / Notas',
                    required: false,
                    placeholder: 'Observaciones de la asignación',
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
        ]);

        $this->hxModalWidth($config->modalWidth);

        return view('components.new-modal', [
            'route' => "assets/{$id}/assignments",
            'config' => $config,
        ]);
    }

    public function asController(Request $request, int $id): Response
    {
        return $this->hxView($this->handle($id));
    }

    public function asStore(Request $request, int $id): JsonResponse
    {
        $input = $request->all();
        $input['hardware'] = filled($input['hardware'] ?? '')
            ? explode(',', $input['hardware'])
            : [];
        $input['software'] = filled($input['software'] ?? '')
            ? explode(',', $input['software'])
            : [];

        $data = Assignment::from($input);

        AssetEvent::create([
            'kind' => 'assignment',
            'asset_id' => $id,
            'employee_id' => $data->employee_id,
            'hardware' => $data->hardware,
            'software' => $data->software,
            'notes' => $data->notes,
            'user_id' => Auth::id(),
        ]);

        Asset::where('id', $id)->update(['status' => 'assigned']);

        $this->hxNotify('Asignación creada correctamente');
        $this->hxRefreshTables(['tabTableAssignments']);
        $this->hxRefresh(['sidebar-summary']);
        $this->hxCloseModals(['modal-body']);

        return $this->hxResponse();
    }
}
