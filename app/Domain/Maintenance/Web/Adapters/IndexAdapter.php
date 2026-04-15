<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Web\Adapters;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Maintenance\Actions\GetMaintenanceDataAction;
use App\Domain\Maintenance\Actions\GetMaintenanceSidebarAction;
use App\Domain\Maintenance\Data\TableData;
use App\Domain\Maintenance\Data\UpsertData;
use App\Domain\Maintenance\Data\SidebarData;
use App\Domain\Shared\Data\ActionOption;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Tabs;
use App\Domain\Shared\Services\SchemaGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\Response;

final class IndexAdapter implements HasDetail, HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route' => 'maintenance',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Mantenimiento',
            icon: 'ri-tools-line',
            subtitle: 'Service Desk de Infraestructura y Maquinaria',
            newButtonLabel: 'Nuevo Ticket',
            modalWidth: '90',
            columns: SchemaGenerator::toColumns(TableData::class),
            formFields: SchemaGenerator::toFields(UpsertData::class),
            tabs: [
                new Tabs(key: 'details', label: 'Info General', icon: 'ri-information-line', route: 'maintenance.details', default: true),
                new Tabs(key: 'tasks', label: 'Tareas y Tiempos', icon: 'ri-time-line', route: 'maintenance.tasks'),
                new Tabs(key: 'history', label: 'Historial', icon: 'ri-history-line', route: 'maintenance.history'),
            ],
            options: [
                new ActionOption(label: 'Ver Detalle', icon: 'ri-search-line', route: 'maintenance', target: '#modal-body', level: 1),
                new ActionOption(label: 'Resolver Ticket', icon: 'ri-edit-line', route: 'maintenance/create', target: '#modal-body', level: 1),
            ],
        );
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $filters = $request->collect('filter')->pluck('value', 'field')->toArray();
        $sorts = $request->collect('sort')->pluck('dir', 'field')->toArray();
        
        $paginator = GetMaintenanceDataAction::run(
            filters: $filters,
            sorts: $sorts,
            page: $request->integer('page', 1),
            size: $request->integer('size', 15)
        );

        return response()->json([
            'data' => $paginator->items(),
            'last_page' => $paginator->lastPage(),
            'last_row' => $paginator->total(),
        ]);
    }

    public function sidebarData(int $id): SidebarData
    {
        return GetMaintenanceSidebarAction::run($id);
    }
}
