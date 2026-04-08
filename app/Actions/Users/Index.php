<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Actions\Password\SendResetLink;
use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Data\Shared\Tabs;
use App\Data\Users\Sidebar;
use App\Data\Users\Table;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Support\HtmxOrchestrator;

final class Index implements HasModule, HasDetail
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        return new Config(
            title: 'Usuarios',
            subtitle: 'Registro de acceso al sistema',
            icon: 'ri-user-settings-line',
            showKpi: true,
            newButtonLabel: 'Nuevo Usuario',
            columns: [
                ['title' => 'ID', 'field' => 'id', 'width' => 70, 'hozAlign' => 'center', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Nombre', 'field' => 'name', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Cédula', 'field' => 'document', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Email', 'field' => 'email', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Estado', 'field' => 'isActive', 'width' => 110, 'hozAlign' => 'center', 'headerHozAlign' => 'center', 'formatter' => 'html', 'headerFilter' => 'list', 'headerFilterParams' => ['values' => [true => 'Activo', false => 'Inactivo'], 'clearable' => true], 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Creado', 'field' => 'createdAt', 'width' => 140, 'hozAlign' => 'center', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ],
            formFields: [
                new Field(name: 'name', label: 'Nombre', required: true, placeholder: 'Nombre completo'),
                new Field(name: 'email', label: 'Email', type: 'email', required: true, placeholder: 'correo@ejemplo.com'),
                new Field(name: 'document', label: 'Cédula', required: true, placeholder: 'Número de documento'),
            ],
            tabs: [
                new Tabs(key: 'info', label: 'Permisos', icon: 'ri-shield-keyhole-line', route: 'users.info', default: true),
            ],
        );
    }

    public function sidebarData(int $id): Sidebar
    {
        return Sidebar::fromModel(User::findOrFail($id));
    }

    public function asController(): View
    {
        return view('components.index', [
            'route' => 'users',
            'config' => $this->config(),
        ]);
    }

    public function asData(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->integer('page', 1));
        $size = max(1, (int) $request->integer('size', 15));
        $offset = ($page - 1) * $size;

        $query = User::query();

        // Filtros Tabulator (Array de Objetos)
        $filters = $request->input('filters', $request->input('filter', []));
        if (is_array($filters)) {
            foreach ($filters as $f) {
                $field = $f['field'] ?? null;
                $value = $f['value'] ?? null;

                if ($value === null || $value === '') {
                    continue;
                }
                
                match ($field) {
                    'name', 'email', 'document' => $query->where($field, 'ilike', '%'.$value.'%'),
                    'isActive' => $query->where('is_active', filter_var($value, FILTER_VALIDATE_BOOLEAN)),
                    'createdAt' => $query->whereDate('created_at', $value),
                    default => null,
                };
            }
        }

        // Ordenamiento con mapeo de campos JS -> DB
        $sortRaw = $request->input('sorters', $request->input('sort', []));
        /** @var array{field?: string, dir?: string} $sort */
        $sort = is_array($sortRaw) && isset($sortRaw[0]) && is_array($sortRaw[0]) ? $sortRaw[0] : [];
        $sortField = match ($sort['field'] ?? '') {
            'isActive' => 'is_active',
            'createdAt' => 'created_at',
            'name', 'email', 'document' => $sort['field'],
            default => 'id'
        };
        $rawDir = $sort['dir'] ?? 'asc';
        $sortDir = in_array(strtolower($rawDir), ['asc', 'desc'], true) ? $rawDir : 'asc';
        $query->orderBy($sortField, $sortDir);

        $total = (clone $query)->count();

        // El map a Table::fromModel() asegura que los datos salgan limpios
        $rows = $query->offset($offset)->limit($size)->get()->map(fn (User $u): Table => Table::fromModel($u));

        return response()->json([
            'data' => $rows->all(),
            'last_page' => (int) ceil($total / $size),
            'last_row' => $total,
        ]);
    }

    public function asResetPassword(string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        
        SendResetLink::run($user->email);

        return $this
            ->hxNotify("Correo de restauración enviado a: {$user->email}")
            ->hxResponse();
    }
}
