<?php

declare(strict_types=1);

namespace App\Domain\Cbm\Web\Adapters;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Cbm\Actions\CalculateCbmPackingAction;
use App\Domain\Cbm\Actions\GetCbmDataAction;
use App\Domain\Cbm\Data\TableData;
use App\Domain\Cbm\Data\UpsertData;
use App\Domain\Shared\Data\ActionOption;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Tabs;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class IndexAdapter implements HasDetail, HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route' => 'cbm',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'CBM - Condition Based Maintenance',
            icon: 'ri-box-3-line',
            subtitle: 'Planificación de Carga y Cubicaje (Items -> Crates -> Container)',
            newButtonLabel: 'Nuevo Proyecto CBM',
            modalWidth: '95',
            columns: SchemaGenerator::toColumns(TableData::class),
            formFields: SchemaGenerator::toFields(UpsertData::class),
            tabs: [
                new Tabs(key: 'packing', label: '3D Load Plan', icon: 'ri-cube-line', route: 'cbm.packing', default: true),
                new Tabs(key: 'list', label: 'Detalle de Items', icon: 'ri-list-check', route: 'cbm.items'),
            ],
            options: [
                new ActionOption(label: 'Ver Plan 3D', icon: 'ri-search-line', route: 'cbm', target: '#modal-body', level: 1),
            ],
        );
    }

    public function sidebarData(int $id): \App\Domain\Cbm\Data\SidebarData
    {
        $cbm = \App\Domain\Cbm\Models\Cbm::findOrFail($id);
        
        return new \App\Domain\Cbm\Data\SidebarData(
            id: (int) $cbm->id,
            title: $cbm->project,
            subtitle: 'CBM Project',
            color: 'black',
            properties: [
                new \App\Domain\Shared\Data\SidebarItem(label: 'Total Items', value: (string) $cbm->total_items),
                new \App\Domain\Shared\Data\SidebarItem(label: 'Creado el', value: $cbm->created_at?->format('Y-m-d') ?? '—'),
            ],
            model: $cbm
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

        /** @var \App\Domain\Shared\Data\PaginatedResult<TableData> $result */
        $result = GetCbmDataAction::run(
            filters: $filters,
            sorts: $sorts,
            page: $request->integer('page', 1),
            size: $request->integer('size', 15),
        );

        return response()->json([
            'data' => $result->items,
            'last_page' => $result->lastPage,
            'last_row' => $result->total,
        ]);
    }
}
