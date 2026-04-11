<?php

declare(strict_types=1);

namespace App\Domain\Assets\Web\Actions;

use App\Domain\Assets\Actions\GetData;
use App\Domain\Assets\Data\Sidebar;
use App\Domain\Assets\Data\Table;
use App\Domain\Assets\Data\UpsertData;
use App\Domain\Assets\Models\Asset;
use App\Domain\Shared\Data\ActionOption;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Tabs;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Index
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route' => 'assets',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Activos',
            subtitle: 'Registro de activos tecnológicos',
            icon: 'ri-stack-line',
            newButtonLabel: 'Nuevo Activo',
            modalWidth: '90',
            columns: \App\Domain\Shared\Services\SchemaGenerator::toColumns(Table::class),
            formFields: \App\Domain\Shared\Services\SchemaGenerator::toFields(UpsertData::class),
            tabs: [
                new Tabs(key: 'details', label: 'Detalles', icon: 'ri-information-line', route: 'assets.details', default: true),
                new Tabs(key: 'movements', label: 'Movimientos', icon: 'ri-arrow-left-right-line', route: 'assets.movements'),
                new Tabs(key: 'documents', label: 'Documentos', icon: 'ri-file-line', route: 'assets.documents'),
                new Tabs(key: 'automations', label: 'Automations', icon: 'ri-settings-4-line', route: 'assets.automations'),
                new Tabs(key: 'maintenances', label: 'Correctivos', icon: 'ri-tools-line', route: 'assets.maintenances'),
                new Tabs(key: 'preventive', label: 'Preventivos', icon: 'ri-calendar-check-line', route: 'assets.preventive'),
                new Tabs(key: 'ai', label: 'SIGMA AI', icon: 'ri-robot-2-line', route: 'assets.ai'),
            ],
            options: [
                new ActionOption(
                    label: 'Editar Activo',
                    icon: 'ri-edit-line',
                    route: 'assets/create',
                    target: '#modal-body',
                    level: 1
                ),
                new ActionOption(
                    label: 'Dar de Baja',
                    icon: 'ri-delete-bin-line',
                    route: 'assets/dispose',
                    target: '#modal-body-2', 
                    level: 2
                ),
            ]
        );
    }

    public function sidebarData(int $id): Sidebar
    {
        $asset = Asset::query()
            ->with(['currentAssignment.employee'])
            ->findOrFail($id);

        return Sidebar::from($asset);
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $paginator = GetData::run([
            'sort' => $request->get('sort', []),
            'filter' => $request->get('filter', []),
            'size' => (int) $request->get('size', 15),
            'page' => (int) $request->get('page', 1),
        ]);

        return response()->json([
            'data' => $paginator['data'],
            'last_page' => $paginator['last_page'],
            'last_row' => $paginator['total'],
        ]);
    }
}
