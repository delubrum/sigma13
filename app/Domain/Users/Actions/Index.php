<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Identity\Actions\Password\SendResetLink;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\Tabs;
use App\Domain\Users\Data\Sidebar;
use App\Domain\Users\Data\Table;
use App\Domain\Users\Models\User;
use App\Support\HtmxOrchestrator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Index implements HasDetail, HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function config(): Config
    {
        return new Config(
            title: 'Usuarios',
            subtitle: 'Registro de acceso al sistema',
            icon: 'ri-user-settings-line',
            newButtonLabel: 'Nuevo Usuario',
            showKpi: true,
            columns: Table::columns(),
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
        return Sidebar::from(User::findOrFail($id));
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

        $query = User::query()->with([]);

        // Filtros Tabulator (Array de Objetos)
        /** @var list<array{field: string, value: mixed}> $filters */
        $filters = (array) $request->input('filters', $request->input('filter', []));
        foreach ($filters as $f) {
            $field = (string) ($f['field'] ?? '');
            $value = $f['value'] ?? null;
            if (blank($value)) {
                continue;
            }

            if (! is_scalar($value)) {
                continue;
            }
            $v = (string) $value;
            match ($field) {
                'name', 'email', 'document' => $query->where($field, 'ilike', "%{$v}%"),
                'isActive' => $query->where('is_active', filter_var($v, FILTER_VALIDATE_BOOLEAN)),
                'createdAt' => $query->whereDate('created_at', $v),
                default => null,
            };
        }

        // Ordenamiento con mapeo de campos JS -> DB
        /** @var list<array{field: string, dir: string}> $sorters */
        $sorters = (array) $request->input('sorters', $request->input('sort', []));
        $sort = $sorters[0] ?? ['field' => 'id', 'dir' => 'desc'];

        $sortField = match ($sort['field'] ?? '') {
            'isActive' => 'is_active',
            'createdAt' => 'created_at',
            'name', 'email', 'document' => $sort['field'],
            default => 'id'
        };
        $sortDir = in_array(strtolower((string) $sort['dir']), ['asc', 'desc'], true) ? $sort['dir'] : 'desc';
        $query->orderBy($sortField, $sortDir);

        $total = (clone $query)->count();

        $rows = $query->offset($offset)->limit($size)->get()->map(fn (User $u): Table => Table::from($u));

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
