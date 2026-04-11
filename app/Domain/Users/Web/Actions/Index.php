<?php

declare(strict_types=1);

namespace App\Domain\Users\Web\Actions;

use App\Domain\Users\Actions\GetUsersData;
use App\Domain\Users\Data\Sidebar;
use App\Domain\Users\Data\Table;
use App\Domain\Users\Data\UpsertData;
use App\Domain\Users\Models\User;
use App\Domain\Shared\Data\ActionOption;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Tabs;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Index
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
            columns: SchemaGenerator::toColumns(Table::class),
            formFields: SchemaGenerator::toFields(UpsertData::class),
            tabs: [
                new Tabs(key: 'info', label: 'Permisos y Acceso', icon: 'ri-shield-keyhole-line', route: 'users.info', default: true),
            ],
            options: [
                new ActionOption(label: 'Editar Perfil', icon: 'ri-edit-line', route: 'users/create', target: '#modal-body', level: 1),
            ]
        );
    }

    public function sidebarData(int $id): Sidebar
    {
        $user = User::findOrFail($id);
        return Sidebar::from($user);
    }

    public function asController(): Response 
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        // Extraemos los parámetros directamente
        $filters = $request->collect('filter')->toArray();
        $sorts   = $request->collect('sort')->toArray();
        $page    = $request->integer('page', 1);
        $size    = $request->integer('size', 15);

        // Ejecutamos la acción pasando los argumentos por separado
        // Nota: He usado GetUsersData que es el nombre que definimos arriba
        $result = GetUsersData::run($filters, $sorts, $page, $size);

        // Como $result ya es un array con 'data', 'last_page' y 'total'...
        return response()->json([
            'data'      => $result['data'],
            'last_page' => $result['last_page'],
            'last_row'  => $result['total'], // Tabulator usa last_row para el total de registros
        ]);
    }
}