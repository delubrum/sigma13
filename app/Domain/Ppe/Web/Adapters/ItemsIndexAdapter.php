<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Web\Adapters;

use App\Domain\Ppe\Data\ItemTableData;
use App\Domain\Ppe\Queries\PpeItemTableQuery;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class ItemsIndexAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route' => 'ppe/items',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'PPE Inventory',
            subtitle: 'Base de datos de implementos de seguridad',
            icon: 'ri-shield-user-line',
            newButtonLabel: 'Nuevo Implemento',
            modalWidth: '50',
            columns: SchemaGenerator::toColumns(ItemTableData::class),
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

        $paginator = PpeItemTableQuery::make()->apply($filters, $sorts)->paginate($request->integer('page', 1), $request->integer('size', 15));

        return response()->json([
            'data'      => $paginator->getCollection()->map(fn($item) => ItemTable::from($item))->toArray(),
            'last_page' => $paginator->lastPage(),
            'last_row'  => $paginator->total(),
        ]);
    }
}
