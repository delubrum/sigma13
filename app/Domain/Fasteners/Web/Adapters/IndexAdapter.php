<?php

declare(strict_types=1);

namespace App\Domain\Fasteners\Web\Adapters;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Fasteners\Actions\GetFastenersDataAction;
use App\Domain\Fasteners\Actions\GetFastenerSidebarAction;
use App\Domain\Fasteners\Data\TableData;
use App\Domain\Fasteners\Data\UpsertData;
use App\Domain\Fasteners\Data\SidebarData;
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
            'route' => 'fasteners',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Tornillería',
            icon: 'ri-command-line',
            subtitle: 'Librería Técnica de Sujetadores y Tornillería',
            newButtonLabel: 'Nuevo Tornillo',
            modalWidth: '90',
            columns: SchemaGenerator::toColumns(TableData::class),
            formFields: SchemaGenerator::toFields(UpsertData::class),
            tabs: [
                new Tabs(key: 'details', label: 'Especificaciones', icon: 'ri-information-line', route: 'fasteners.details', default: true),
                new Tabs(key: 'files', label: 'Archivos y Planos', icon: 'ri-file-line', route: 'fasteners.files'),
            ],
            options: [
                new ActionOption(label: 'Editar Ficha', icon: 'ri-edit-line', route: 'fasteners/create', target: '#modal-body', level: 1),
            ],
        );
    }

    public function sidebarData(int $id): SidebarData
    {
        /** @var SidebarData $result */
        $result = GetFastenerSidebarAction::run($id);

        return $result;
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
        $result = GetFastenersDataAction::run(
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
