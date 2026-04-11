<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Assets\Data\Sidebar;
use App\Domain\Assets\Data\Table;
use App\Domain\Assets\Data\UpsertData;
use App\Domain\Assets\Models\Asset;
use App\Domain\Shared\Data\ActionOption;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Tabs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

final class Index implements HasDetail, HasModule
{
    use AsAction;

    public function handle(): Config
    {
        return $this->config();
    }

    public function config(): Config
    {
        return new Config(
            title: 'Activos',
            subtitle: 'Registro de activos tecnológicos',
            icon: 'ri-stack-line',
            newButtonLabel: 'Nuevo Activo',
            modalWidth: '90',
            columns: Table::columns(),
            formFields: UpsertData::fields(),
            tabs: [
                new Tabs(key: 'details', label: 'Detalles', icon: 'ri-information-line', route: 'assets.details', default: true),
                new Tabs(key: 'movements', label: 'Movimientos', icon: 'ri-arrow-left-right-line', route: 'assets.movements'),
                new Tabs(key: 'documents', label: 'Documentos', icon: 'ri-file-line', route: 'assets.documents'),
                new Tabs(key: 'automations', label: 'Automations', icon: 'ri-settings-4-line', route: 'assets.automations'),
                new Tabs(key: 'maintenances', label: 'Correctivos', icon: 'ri-tools-line', route: 'assets.maintenances'),
                new Tabs(key: 'preventive', label: 'Preventivos', icon: 'ri-calendar-check-line', route: 'assets.preventive'),
                new Tabs(key: 'ai', label: 'SIGMA AI', icon: 'ri-robot-2-line', route: 'assets.ai'),
            ],
            options: [
                new ActionOption(
                    label: 'Editar Activo',
                    icon: 'ri-edit-line',
                    route: 'assets/create', // Usamos URL relativa para el orquestador global
                    target: '#modal-body',
                    level: 1
                ),
                new ActionOption(
                    label: 'Dar de Baja',
                    icon: 'ri-delete-bin-line',
                    route: 'assets/dispose', // Ejemplo de otra acción
                    target: '#modal-body-2', 
                    level: 2
                ),
            ]
        );
    }

    public function sidebarData(int $id): Sidebar
    {
        $asset = Asset::query()
            ->with(['currentAssignment.employee'])
            ->findOrFail($id);

        return Sidebar::from($asset);
    }

    public function asData(Request $request): JsonResponse
    {
        $size = max(1, $request->integer('size', 15));
        $page = max(1, $request->integer('page', 1));

        $query = Asset::query()->with(['currentAssignment.employee']);

        /** @var list<array{field: string, value: mixed}> $filters */
        $filters = (array) $request->input('filters', $request->input('filter', []));
        $this->applyFilters($query, $filters);

        /** @var list<array{field: string, dir: string}> $sorters */
        $sorters = (array) $request->input('sorters', $request->input('sort', []));
        $sort = $sorters[0] ?? ['field' => 'id', 'dir' => 'desc'];

        $field = (string) ($sort['field'] ?? 'id');
        $dir = strtolower((string) ($sort['dir'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
        $table = $query->getModel()->getTable();

        match ($field) {
            'criticality' => $query->orderByCriticality($dir),
            'assignee' => $query->orderByAssignee($dir),
            'work_mode' => $query->orderBy("$table.work_mode", $dir),
            'date' => $query->orderBy("$table.acquisition_date", $dir),
            default => $query->orderBy("$table.$field", $dir),
        };

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginator->map(fn (Asset $asset) => Table::fromModel($asset))->values(),
            'last_page' => $paginator->lastPage(),
            'last_row' => $paginator->total(),
        ]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Asset>  $query
     * @param  list<array{field: string, value: mixed}>  $filters
     */
    private function applyFilters(\Illuminate\Database\Eloquent\Builder $query, array $filters): void
    {
        foreach ($filters as $f) {
            $field = $f['field'] ?? null;
            $value = $f['value'] ?? null;

            if (trim((string) ($value ?? '')) === '') {
                continue;
            }

            if (! is_scalar($value)) {
                continue;
            }

            $valStr = (string) $value;

            match ($field) {
                'id' => $query->where('id', (int) $value),
                'status' => $query->where('status', $valStr),
                'work_mode' => $query->where('work_mode', 'ilike', "%{$valStr}%"),
                'confidentiality', 'integrity', 'availability' => $query->where($field, (int) $value),
                'criticality' => $query->whereRaw('(confidentiality + integrity + availability) = ?', [(int) $value]),
                'date', 'acquisition_date' => str_contains($valStr, ' to ')
                    ? $query->whereBetween('acquisition_date', explode(' to ', $valStr))
                    : $query->whereDate('acquisition_date', $valStr),
                'assignee' => $query->whereHas('currentAssignment.employee', fn (\Illuminate\Database\Eloquent\Builder $q) => $q->where('name', 'ilike', "%{$valStr}%")),
                default => $query->where($field, 'ilike', "%{$valStr}%"),
            };
        }
    }
}
