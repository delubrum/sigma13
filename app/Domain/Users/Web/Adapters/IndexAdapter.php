<?php

declare(strict_types=1);

namespace App\Domain\Users\Web\Adapters;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Data\Tabs;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Domain\Users\Actions\GetUsersDataAction;
use App\Domain\Users\Actions\GetUserSidebarAction;
use App\Domain\Users\Data\SidebarData;
use App\Domain\Users\Data\TableData;
use App\Domain\Users\Data\UpsertData;
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
            'route' => 'users',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Usuarios',
            icon: 'ri-user-settings-line',
            subtitle: 'Gestión de accesos y permisos',
            newButtonLabel: 'Nuevo Usuario',
            modalWidth: '50',
            columns: SchemaGenerator::toColumns(TableData::class),
            formFields: SchemaGenerator::toFields(UpsertData::class),
            tabs: [
                new Tabs(key: 'general', label: 'Información', icon: 'ri-information-line', route: 'users.general'),
                new Tabs(key: 'info', label: 'Permisos y Acceso', icon: 'ri-shield-keyhole-line', route: 'users.info', default: true),
            ]
        );
    }

    public function sidebarData(int $id): SidebarData
    {
        /** @var SidebarData $result */
        $result = GetUserSidebarAction::run($id);

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

        /** @var PaginatedResult<TableData> $result */
        $result = GetUsersDataAction::run(
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
