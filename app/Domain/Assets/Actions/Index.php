<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Assets\Data\Sidebar;
use App\Domain\Assets\Data\Table;
use App\Domain\Assets\Models\Asset;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Tabs;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Index implements HasDetail, HasModule
{
    use AsAction;

    public function config(): Config
    {
        return new Config(
            title: 'Activos',
            subtitle: 'Registro de activos tecnológicos',
            icon: 'ri-stack-line',
            newButtonLabel: 'Nuevo Activo',
            columns: Table::columns(),
            tabs: [
                new Tabs(key: 'details', label: 'Detalles', icon: 'ri-information-line', route: 'assets.details', default: true),
                new Tabs(key: 'movements', label: 'Movimientos', icon: 'ri-arrow-left-right-line', route: 'assets.movements'),
                new Tabs(key: 'documents', label: 'Documentos', icon: 'ri-file-line', route: 'assets.documents'),
                new Tabs(key: 'automations', label: 'Automations', icon: 'ri-settings-4-line', route: 'assets.automations'),
                new Tabs(key: 'maintenances', label: 'Correctivos', icon: 'ri-tools-line', route: 'assets.maintenances'),
                new Tabs(key: 'preventive', label: 'Preventivos', icon: 'ri-calendar-check-line', route: 'assets.preventive'),
                new Tabs(key: 'ai', label: 'SIGMA AI', icon: 'ri-robot-2-line', route: 'assets.ai'),
            ],
        );
    }

    public function asController(Request $request): View
    {
        return view('components.index', [
            'route' => 'assets',
            'config' => $this->config(),
        ]);
    }

    public function asData(Request $request): JsonResponse
    {
        // 1. Paginación y Query Base
        $size = $request->integer('size', 15);
        $query = Asset::query()->with(['currentAssignment.employee']);

        // 2. Aplicar Filtros (Tabulator 6.4 usa 'filter')
        /** @var list<array{field: string, value: mixed}> $filters */
        $filters = (array) $request->input('filter', []);
        $this->applyFilters($query, $filters);

        // 3. Aplicar Ordenamiento (Tabulator 6.4 usa 'sort')
        /** @var list<array{field: string, dir: string}> $sorters */
        $sorters = (array) $request->input('sort', []);
        $sort = $sorters[0] ?? ['field' => 'id', 'dir' => 'desc'];

        $field = (string) ($sort['field'] ?? 'id');
        $dir = strtolower((string) ($sort['dir'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
        $table = $query->getModel()->getTable();

        match ($field) {
            'criticality' => $query->orderByCriticality($dir),
            'assignee' => $query->orderByAssignee($dir),
            'workMode' => $query->orderBy("$table.work_mode", $dir),
            'date' => $query->orderBy("$table.acquisition_date", $dir),
            default => $query->orderBy("$table.$field", $dir),
        };

        // 4. Paginación Nativa y Respuesta con DTO
        $paginator = $query->paginate($size);

        return response()->json([
            'data' => Table::collect($paginator->items()),
            'last_page' => $paginator->lastPage(),
        ]);
    }

    /**
     * @param  Builder<Asset>  $query
     * @param  list<array{field: string, value: mixed}>  $filters
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $f) {
            $field = $f['field'] ?? null;
            $value = $f['value'] ?? null;

            if (blank($value)) {
                continue;
            }

            if (! is_scalar($value)) {
                continue;
            }

            $valStr = (string) $value;

            match ($field) {
                'status' => $query->where('status', $valStr),
                'workMode' => $query->where('work_mode', 'ilike', "%{$valStr}%"),

                'confidentiality', 'integrity', 'availability' => $query->where($field, (int) $value),

                'date', 'acquisition_date' => str_contains($valStr, ' to ')
                    ? $query->whereBetween('acquisition_date', explode(' to ', $valStr))
                    : $query->whereDate('acquisition_date', $valStr),

                'assignee' => $query->whereHas('currentAssignment.employee', fn ($q) => $q->where('name', 'ilike', "%{$valStr}%")
                ),

                // Filtro genérico para campos de texto (PostgreSQL ilike)
                default => $query->where($field, 'ilike', "%{$valStr}%"),
            };
        }
    }

    public function sidebarData(int $id): Sidebar
    {
        $asset = Asset::query()->with(['currentAssignment.employee'])->findOrFail($id);

        return Sidebar::from([
            ...$asset->attributesToArray(),
            'assignee_name' => $asset->assignee_name,
            'qrUrl' => route('detail', ['route' => 'assets', 'id' => $asset->id]),
        ]);
    }
}
