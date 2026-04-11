<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions\Modals;

use App\Domain\Assets\Actions\RegisterAssignment;
use App\Domain\Assets\Data\Modals\AssignmentData;
use App\Domain\Shared\Data\Config;
use App\Domain\Users\Models\Employee;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

final class Assignment
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        /** @var array<string, string> $employees */
        $employees = once(fn (): array => Employee::orderBy('name')
            ->get()
            ->mapWithKeys(fn (Employee $e): array => [(string) $e->id => "{$e->id} || {$e->name}"])
            ->all());

        return new Config(
            title: 'Nueva Asignación',
            subtitle: 'Registro de asignación de activo',
            icon: 'ri-user-add-line',
            modalWidth: '50',
            formFields: \App\Domain\Shared\Services\SchemaGenerator::toFields(AssignmentData::class, [
                'employee_id' => ['options' => $employees],
            ]),
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

    public function asController(int $id): Response
    {
        return $this->hxView($this->handle($id));
    }

    public function asStore(Request $request, int $id): JsonResponse|Response
    {
        try {
            $data = AssignmentData::from($request->all());

            RegisterAssignment::run($id, $data, (int) Auth::id());

            // Tu Orchestrator con Notyf
            $this->hxNotify('Asignación creada correctamente');
            $this->hxRefreshTables(['tabTableMovements']);
            $this->hxRefresh(['sidebar-summary']);
            $this->hxCloseModals(['modal-body-2']);

            return $this->hxResponse();

        } catch (ValidationException $e) {
            // Manejo de error 422: Swap del formulario sin cerrar el modal
            return response()
                ->view('assets::modals.assignment-form', [
                    'config' => $this->config(),
                    'errors' => $e->validator->errors(),
                    'assetId' => $id,
                ], 422)
                ->header('HX-Retarget', '#assignment-form');
        }
    }
}