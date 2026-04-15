<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Web\Adapters;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Ppe\Actions\GetPpeEntryDataAction;
use App\Domain\Ppe\Actions\GetPpeMovementsDataAction;
use App\Domain\Ppe\Data\PpeEntryTableData;
use App\Domain\Ppe\Data\PpeEntryUpsertData;
use App\Domain\Ppe\Data\PpeMovementTableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Data\Tabs;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\Data;

final class PpeEntriesIndexAdapter implements HasDetail, HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route'  => 'ppe-entries',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'EPP / Ingresos',
            icon: 'ri-arrow-down-circle-line',
            subtitle: 'Stock de elementos por ítem',
            newButtonLabel: 'Nuevo Ingreso',
            columns: SchemaGenerator::toColumns(PpeEntryTableData::class),
            formFields: SchemaGenerator::toFields(PpeEntryUpsertData::class),
            tabs: [
                new Tabs(
                    key: 'movements',
                    label: 'Movimientos',
                    icon: 'ri-swap-line',
                    route: 'ppe-entries.tab.movements',
                    default: true,
                ),
            ],
        );
    }

    public function sidebarData(int $id): Data
    {
        return new \App\Domain\Ppe\Data\PpeEntryTableData(
            id:    (string) $id,
            name:  '',
            stock: '',
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

        /** @var PaginatedResult<PpeEntryTableData> $result */
        $result = GetPpeEntryDataAction::run(
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

    public function movementsData(int $id, Request $request): JsonResponse
    {
        $filters = $request->collect('filter')->pluck('value', 'field')->toArray();
        $sorts   = $request->collect('sort')->pluck('dir', 'field')->toArray();

        /** @var PaginatedResult<PpeMovementTableData> $result */
        $result = GetPpeMovementsDataAction::run(
            itemId:  $id,
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
