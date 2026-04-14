<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Web\Adapters;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Improvement\Actions\GetImprovementDataAction;
use App\Domain\Improvement\Actions\GetImprovementSidebarAction;
use App\Domain\Improvement\Data\SidebarData;
use App\Domain\Improvement\Data\TableData;
use App\Domain\Improvement\Data\UpsertData;
use App\Domain\Shared\Data\ActionOption;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
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
            'route'  => 'improvement',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title:          'Mejoras Continuas',
            icon:           'ri-lightbulb-line',
            subtitle:       'Gestión de acciones de mejora y correctivas',
            newButtonLabel: 'Nueva Mejora',
            modalWidth:     '70',
            columns:        SchemaGenerator::toColumns(TableData::class),
            formFields:     SchemaGenerator::toFields(UpsertData::class),
            tabs: [
                new Tabs(key: 'causes',     label: 'Causas',      icon: 'ri-search-eye-line',  route: 'improvement.causes',     default: true),
                new Tabs(key: 'activities', label: 'Actividades',  icon: 'ri-task-line',        route: 'improvement.activities', default: false),
            ],
            options: [
                new ActionOption(
                    label:  'Rechazar',
                    icon:   'ri-close-circle-line',
                    route:  'improvement/reject',
                    target: '#modal-body-2',
                    level:  2,
                    method: 'GET',
                ),
                new ActionOption(
                    label:  'Cancelar',
                    icon:   'ri-forbid-line',
                    route:  'improvement/cancel',
                    target: '#modal-body-2',
                    level:  2,
                    method: 'GET',
                ),
                new ActionOption(
                    label:  'Cerrar Mejora',
                    icon:   'ri-checkbox-circle-line',
                    route:  'improvement/close',
                    target: '#modal-body-2',
                    level:  2,
                    method: 'GET',
                ),
            ],
        );
    }

    public function sidebarData(int $id): SidebarData
    {
        /** @var SidebarData $result */
        $result = GetImprovementSidebarAction::run($id);

        return $result;
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $filters = $request->collect('filter')->pluck('value', 'field')->toArray();
        $sorts   = $request->collect('sort')->pluck('dir', 'field')->toArray();

        /** @var PaginatedResult<TableData> $result */
        $result = GetImprovementDataAction::run(
            filters: $filters,
            sorts:   $sorts,
            page:    $request->integer('page', 1),
            size:    $request->integer('size', 15),
        );

        return response()->json([
            'data'      => $result->items,
            'last_page' => $result->lastPage,
            'last_row'  => $result->total,
        ]);
    }
}
