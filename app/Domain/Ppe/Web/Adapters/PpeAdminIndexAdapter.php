<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Web\Adapters;

use App\Contracts\HasModule;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Domain\Ppe\Actions\GetPpeItemDataAction;
use App\Domain\Ppe\Data\PpeItemTableData;
use App\Domain\Ppe\Data\PpeItemUpsertData;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class PpeAdminIndexAdapter implements HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route'  => 'ppe-admin',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'EPP / Catálogo',
            icon: 'ri-shield-check-line',
            subtitle: 'Gestión de elementos de protección personal',
            newButtonLabel: 'Nuevo EPP',
            columns: SchemaGenerator::toColumns(PpeItemTableData::class),
            formFields: SchemaGenerator::toFields(PpeItemUpsertData::class),
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

        /** @var PaginatedResult<PpeItemTableData> $result */
        $result = GetPpeItemDataAction::run(
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
