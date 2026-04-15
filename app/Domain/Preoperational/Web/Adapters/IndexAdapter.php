<?php

declare(strict_types=1);

namespace App\Domain\Preoperational\Web\Adapters;

use App\Contracts\HasModule;
use App\Domain\Preoperational\Actions\GetPreoperationalsDataAction;
use App\Domain\Preoperational\Data\TableData;
use App\Domain\Shared\Data\ActionOption;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class IndexAdapter implements HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route' => 'preoperational',
            'config' => $this->config(),
            'enableTabs' => false,
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Preoperacionales',
            icon: 'ri-truck-line',
            subtitle: 'Registro y control de inspecciones preoperacionales',
            newButtonLabel: 'Nuevo Preoperacional',
            modalWidth: '50', // Custom size for "New Preoperational"
            columns: SchemaGenerator::toColumns(TableData::class),
            options: [
                new ActionOption(label: 'Ver Detalle', icon: 'ri-eye-line', route: 'preoperational/detail', target: '#modal-body', level: 1),
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

        /** @var PaginatedResult<TableData> $result */
        $result = GetPreoperationalsDataAction::run(
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
