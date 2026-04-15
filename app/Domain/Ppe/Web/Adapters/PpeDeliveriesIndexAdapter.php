<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Web\Adapters;

use App\Contracts\HasModule;
use App\Domain\Ppe\Actions\GetPpeDeliveryDataAction;
use App\Domain\Ppe\Data\PpeDeliveryTableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class PpeDeliveriesIndexAdapter implements HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route'  => 'ppe-deliveries',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'EPP / Entregas',
            icon: 'ri-hand-heart-line',
            subtitle: 'Registro de entregas a empleados',
            newButtonLabel: 'Nueva Entrega',
            columns: SchemaGenerator::toColumns(PpeDeliveryTableData::class),
            formFields: [],
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

        /** @var PaginatedResult<PpeDeliveryTableData> $result */
        $result = GetPpeDeliveryDataAction::run(
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
