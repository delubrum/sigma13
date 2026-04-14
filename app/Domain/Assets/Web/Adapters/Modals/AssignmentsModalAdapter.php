<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Adapters\Modals;

use App\Domain\Assets\Actions\GetAssignmentEventAction;
use App\Domain\Assets\Actions\RegisterAssignmentAction;
use App\Domain\Assets\Actions\UpdateAssignmentAction;
use App\Domain\Assets\Data\Modals\AssignmentModalData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class AssignmentsModalAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        once(fn (): array => DB::table('employees')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (object $e): array => [(string) $e->id => "{$e->id} || {$e->name}"])
            ->all());

        return new Config(
            title: 'Nueva Asignación',
            icon: 'ri-user-add-line',
            subtitle: 'Registro de asignación de activo',
            modalWidth: '50',
            formFields: SchemaGenerator::toFields(AssignmentModalData::class),
        );
    }

    public function handle(int $id): Response
    {
        $config = $this->config();
        $this->hxModalWidth($config->modalWidth, '-2');
        $this->hxTriggers['open-modal-2'] = true;

        return $this->hxView('components.new-modal', [
            'route' => "assets/{$id}/assignments",
            'config' => $config,
            'target' => '#modal-body-2',
            'closeEvent' => 'close-modal-2',
            'suffix' => '-2',
        ]);
    }

    public function asController(int $id): Response
    {
        return $this->handle($id);
    }

    public function asStore(Request $request, int $id): JsonResponse
    {
        $data = AssignmentModalData::from($request->all());

        RegisterAssignmentAction::run($id, $data, (int) Auth::id());

        $this->hxNotify('Asignación creada correctamente');
        $this->hxRefreshTables(['tabTableMovements']);
        $this->hxRefresh(['sidebar-summary']);
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }

    public function asEdit(int $event): Response
    {
        $data = GetAssignmentEventAction::run($event);
        $config = $this->config();

        $this->hxModalWidth($config->modalWidth, '-2');
        $this->hxTriggers['open-modal-2'] = true;

        return $this->hxView('components.new-modal', [
            'route' => "assets/assignments/{$event}",
            'method' => 'PATCH',
            'config' => $config,
            'values' => $data,
            'target' => '#modal-body-2',
            'closeEvent' => 'close-modal-2',
            'suffix' => '-2',
        ]);
    }

    public function asUpdate(Request $request, int $event): JsonResponse
    {
        $data = AssignmentModalData::from($request->all());

        UpdateAssignmentAction::run($event, $data, (int) Auth::id());

        $this->hxNotify('Asignación actualizada');
        $this->hxRefreshTables(['tabTableMovements']);
        $this->hxCloseModals(['modal-body-2']);

        return $this->hxResponse();
    }
}
