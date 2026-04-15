<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Web\Adapters;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Extrusion\Actions\AiDataAction;
use App\Domain\Extrusion\Actions\AiSearchAction;
use App\Domain\Extrusion\Actions\GetDiesDataAction;
use App\Domain\Extrusion\Actions\GetDieSidebarAction;
use App\Domain\Extrusion\Data\SidebarData;
use App\Domain\Extrusion\Data\TableData;
use App\Domain\Extrusion\Data\UpsertData;
use App\Domain\Extrusion\Queries\FilterOptionsQuery;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\Data;

final class IndexAdapter implements HasDetail, HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route'  => 'extrusion',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Extrusion Dies',
            icon: 'ri-layout-grid-line',
            subtitle: 'Technical Library — Matrices de extrusión',
            newButtonLabel: 'New Die',
            modalWidth: '90',
            columns: SchemaGenerator::toColumns(TableData::class),
            formFields: SchemaGenerator::toFields(UpsertData::class),
        );
    }

    public function sidebarData(int $id): Data
    {
        /** @var SidebarData $data */
        $data = GetDieSidebarAction::run($id);

        return $data;
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
        $result = GetDiesDataAction::run(
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

    public function filterOptions(Request $request): JsonResponse
    {
        /** @var array<string,string> $active */
        $active = $request->input('filter', []);

        return response()->json(
            (new FilterOptionsQuery)->get($active)
        );
    }

    public function aiSearch(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));

        if (blank($q)) {
            return response()->json([]);
        }

        return response()->json(AiSearchAction::run($q));
    }

    public function aiData(Request $request): JsonResponse
    {
        $raw    = (string) $request->input('params', '{}');
        /** @var array<string,mixed> $params */
        $params = json_decode($raw, true) ?? [];

        /** @var PaginatedResult<TableData> $result */
        $result = AiDataAction::run(
            params: $params,
            page:   $request->integer('page', 1),
            size:   $request->integer('size', 15),
        );

        return response()->json([
            'data'      => $result->items,
            'last_page' => $result->lastPage,
            'last_row'  => $result->total,
        ]);
    }
}
