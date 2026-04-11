<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Web\Actions;

use App\Domain\Ppe\Data\EntryTable;
use App\Domain\Ppe\Queries\PpeStockQuery;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class EntriesIndex
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route' => 'ppe/entries',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'PPE Stock Entries',
            subtitle: 'Control de inventario y entradas de almacén',
            icon: 'ri-hand-coin-line',
            newButtonLabel: 'Nueva Entrada',
            modalWidth: '50',
            columns: SchemaGenerator::toColumns(EntryTable::class),
            formFields: [],
            tabs: [],
            options: []
        );
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $filters = $request->collect('filter')->pluck('value', 'field')->toArray();
        $sorts   = $request->collect('sort')->pluck('dir', 'field')->toArray();

        $paginator = PpeStockQuery::stocks($request->integer('page', 1), $request->integer('size', 15), $filters, $sorts);

        return response()->json([
            'data'      => $paginator->getCollection()->map(fn($row) => new EntryTable(
                id:   $row->id,
                name: $row->name,
                total: (int) (($row->total_in ?? 0) - ($row->total_out ?? 0))
            ))->toArray(),
            'last_page' => $paginator->lastPage(),
            'last_row'  => $paginator->total(),
        ]);
    }
}
