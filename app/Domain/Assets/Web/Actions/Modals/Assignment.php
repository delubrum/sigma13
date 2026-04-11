<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Modals;

use App\Domain\Assets\Actions\RegisterAssignment;
use App\Domain\Assets\Data\Modals\Assignment as AssignmentData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Domain\Users\Models\Employee;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Assignment
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        $employees = once(fn (): array => Employee::orderBy('name')->get()
            ->mapWithKeys(fn ($e): array => [(string) $e->id => "{$e->id} || {$e->name}"])
            ->all());

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
                    type: 'select',
                    required: true,
                    placeholder: 'Seleccionar empleado',
                    options: $employees,
                ),
                new Field(
                    name: 'hardware',
                    label: 'Hardware',
                    type: 'tags',
                    required: false,
                    placeholder: 'Selecciona o escribe el hardware',
                ),
                new Field(
                    name: 'software',
                    label: 'Software',
                    type: 'tags',
                    required: false,
                    placeholder: 'Selecciona o escribe el software',
                ),
                new Field(
                    name: 'notes',
                    label: 'Acta / Notas',
                    type: 'textarea',
                    required: false,
                    placeholder: 'Observaciones de la asignación',
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
            'route' => "assets/{$id}/assignments",
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
        $data = AssignmentData::from($request->all());

        // Delegación a la Core Action
        RegisterAssignment::run($id, $data);

        $this->hxNotify('Asignación creada correctamente');
        $this->hxRefreshTables(['tabTableMovements']);
        $this->hxRefresh(['sidebar-summary']);
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }
}
