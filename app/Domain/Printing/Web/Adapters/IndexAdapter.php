<?php

declare(strict_types=1);

namespace App\Domain\Printing\Web\Adapters;

use App\Contracts\HasModule;
use App\Domain\Printing\Actions\GetWoDataAction;
use App\Domain\Printing\Data\TableData;
use App\Domain\Printing\Data\UpsertData;
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
            'route' => 'printing',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Etiquetas',
            icon: 'ri-printer-line',
            subtitle: 'Impresión de Etiquetas',
            newButtonLabel: 'Nueva WO',
            modalWidth: '30',
            columns: SchemaGenerator::toColumns(TableData::class),
            formFields: UpsertData::fields(),
            multipart: true,
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
        $result = GetWoDataAction::run(
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
