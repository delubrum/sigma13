<?php

declare(strict_types=1);

namespace App\Domain\Users\Web\Adapters;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Users\Actions\GetUserSidebarAction;
use App\Domain\Users\Actions\GetUsersDataAction;
use App\Domain\Users\Data\SidebarData;
use App\Domain\Users\Data\TableData;
use App\Domain\Users\Data\UpsertData;
use App\Domain\Shared\Data\ActionOption;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Tabs;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class IndexAdapter implements HasModule, HasDetail
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
            subtitle: 'Gestión de accesos y permisos',
            icon: 'ri-user-settings-line',
            newButtonLabel: 'Nuevo Usuario',
            modalWidth: '50',
            columns: SchemaGenerator::toColumns(TableData::class),
            formFields: SchemaGenerator::toFields(UpsertData::class),
            tabs: [
                new Tabs(key: 'info', label: 'Permisos y Acceso', icon: 'ri-shield-keyhole-line', route: 'users.info', default: true),
            ],
            options: [
                new ActionOption(label: 'Editar Perfil', icon: 'ri-edit-line', route: 'users/create', target: '#modal-body', level: 1),
            ]
        );
    }

    public function sidebarData(int $id): SidebarData
    {
        return GetUserSidebarAction::run($id);
    }

    public function asController(): Response 
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $filters = $request->collect('filter')->pluck('value', 'field')->toArray();
        $sorts   = $request->collect('sort')->pluck('dir', 'field')->toArray();

        $result = GetUsersDataAction::run(
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