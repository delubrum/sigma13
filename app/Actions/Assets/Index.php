<?php

declare(strict_types=1);

namespace App\Actions\Assets;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Data\Assets\Sidebar;
use App\Data\Assets\Table;
use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Data\Shared\Tabs;
use App\Models\Asset;
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
            columns: [
                ['title' => 'ID', 'field' => 'id', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Área', 'field' => 'area', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'SAP', 'field' => 'sap', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Serial', 'field' => 'serial', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Responsable', 'field' => 'assignee', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Hostname', 'field' => 'hostname', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Marca', 'field' => 'brand', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Modelo', 'field' => 'model', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Tipo', 'field' => 'kind', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'CPU', 'field' => 'cpu', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'RAM', 'field' => 'ram', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'SSD', 'field' => 'ssd', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'HDD', 'field' => 'hdd', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'S.O.', 'field' => 'so', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Precio', 'field' => 'price', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Fecha', 'field' => 'date', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Factura', 'field' => 'invoice', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Proveedor', 'field' => 'supplier', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Garantía', 'field' => 'warranty', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Modo Trabajo', 'field' => 'work_mode', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Ubicación', 'field' => 'location', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Teléfono', 'field' => 'phone', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Operador', 'field' => 'operator', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Estado', 'field' => 'status', 'formatter' => 'html', 'headerHozAlign' => 'center', 'hozAlign' => 'center', 'headerFilter' => 'list', 'headerFilterParams' => ['values' => ['available' => 'Disponible', 'assigned' => 'Asignado', 'maintenance' => 'Mantenimiento', 'retired' => 'Retirado'], 'clearable' => true], 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Clasificación', 'field' => 'classification', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Conf.', 'field' => 'confidentiality', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Int.', 'field' => 'integrity', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Disp.', 'field' => 'availability', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
                ['title' => 'Criticidad', 'field' => 'criticality', 'formatter' => 'html', 'headerHozAlign' => 'center', 'hozAlign' => 'center', 'headerFilter' => 'input', 'headerFilterPlaceholder' => 'Filtro...'],
            ],
            formFields: [
                new Field(name: 'area', label: 'Área', required: true, placeholder: 'Área o departamento'),
                new Field(name: 'hostname', label: 'Hostname', required: true, placeholder: 'Nombre del equipo'),
                new Field(name: 'serial', label: 'Serial', required: true, placeholder: 'Número de serie'),
                new Field(name: 'brand', label: 'Marca', required: true, placeholder: 'Marca del equipo'),
                new Field(name: 'model', label: 'Modelo', required: true, placeholder: 'Modelo del equipo'),
                new Field(name: 'kind', label: 'Tipo', required: true, placeholder: 'Tipo de activo'),
                new Field(name: 'cpu', label: 'CPU', required: false, placeholder: 'Procesador'),
                new Field(name: 'ram', label: 'RAM', required: false, placeholder: 'Memoria RAM'),
                new Field(name: 'ssd', label: 'SSD', required: false, placeholder: 'Capacidad SSD'),
                new Field(name: 'hdd', label: 'HDD', required: false, placeholder: 'Capacidad HDD'),
                new Field(name: 'so', label: 'S.O.', required: false, placeholder: 'Sistema Operativo'),
                new Field(name: 'sap', label: 'Código SAP', required: false, placeholder: 'ID SAP del activo'),
                new Field(name: 'price', label: 'Precio', required: false, placeholder: '0.00'),
                new Field(name: 'date', label: 'Fecha Compra', required: false, placeholder: 'AAAA-MM-DD'),
                new Field(name: 'invoice', label: 'Factura', required: false, placeholder: 'Número de factura'),
                new Field(name: 'supplier', label: 'Proveedor', required: false, placeholder: 'Nombre del proveedor'),
                new Field(name: 'warranty', label: 'Garantía', required: false, placeholder: 'Meses o fecha'),
                new Field(name: 'status', label: 'Estado', required: true, placeholder: 'assigned, storage, retired'),
                new Field(name: 'classification', label: 'Clasificación', required: false, placeholder: 'Categoría'),
                new Field(name: 'confidentiality', label: 'Confidencialidad', required: false, placeholder: '1-3'),
                new Field(name: 'integrity', label: 'Integridad', required: false, placeholder: '1-3'),
                new Field(name: 'availability', label: 'Disponibilidad', required: false, placeholder: '1-3'),
                new Field(name: 'location', label: 'Ubicación', required: false, placeholder: 'Sede o puesto'),
                new Field(name: 'phone', label: 'Teléfono', required: false, placeholder: 'Extensión o móvil'),
                new Field(name: 'work_mode', label: 'Modalidad', required: false, placeholder: 'Presencial, Remoto'),
                new Field(name: 'url', label: 'URL Docs', required: false, placeholder: 'Link documentación'),
                new Field(name: 'operator', label: 'Operador', required: false, placeholder: 'Nombre del operador'),
            ],
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
        $page = max(1, (int) $request->integer('page', 1));
        $size = max(1, (int) $request->integer('size', 15));
        $offset = ($page - 1) * $size;

        $query = Asset::query()->with(['currentAssignment.employee']);

        // Filtros Tabulator (Array de Objetos)
        $filters = $request->input('filters', $request->input('filter', []));
        if (is_array($filters)) {

            foreach ($filters as $f) {
                $field = $f['field'] ?? null;
                $value = $f['value'] ?? null;

                if ($value === null || $value === '') {
                    continue;
                }

                $valStr = (string) $value;

                match ($field) {
                    'area', 'hostname', 'serial', 'sap', 'brand', 'model', 'kind',
                    'cpu', 'ram', 'ssd', 'hdd', 'so', 'invoice', 'supplier',
                    'location', 'operator', 'classification', 'work_mode', 'phone' => $query->where($field, 'ilike', "%{$valStr}%"),

                    'status' => $query->where('status', $valStr),
                    'confidentiality', 'integrity', 'availability' => $query->where($field, (int) $value),

                    'date', 'acquisition_date' => str_contains($valStr, ' to ')
                        ? $query->whereBetween('acquisition_date', explode(' to ', $valStr))
                        : $query->whereDate('acquisition_date', $valStr),

                    'assignee' => $query->whereHas('currentAssignment.employee', function (Builder $q) use ($valStr): void {
                        $q->where('name', 'ilike', "%{$valStr}%");
                    }),

                    default => null
                };
            }
        }

        /** @var array{field: string, dir: string} $sort */
        $sort = collect($request->array('sorters'))->first() ?? ['field' => 'id', 'dir' => 'desc'];
        $sortDir = in_array(strtolower($sort['dir'] ?? ''), ['asc', 'desc']) ? $sort['dir'] : 'desc';

        if ($sort['field'] === 'criticality') {
            $dir = $sortDir === 'desc' ? 'DESC' : 'ASC';
            $query->orderByRaw('(confidentiality + integrity + availability) '.$dir);
        } elseif ($sort['field'] === 'assignee') {
            $query->orderBy('id', $sortDir);
        } else {
            $query->orderBy($sort['field'] ?? 'id', $sortDir);
        }

        $total = $query->count();
        $rows = $query->offset($offset)
            ->limit($size)
            ->get()
            ->map(fn (Asset $asset): Table => Table::fromModel($asset));

        return response()->json([
            'data'      => $rows->values()->all(),
            'last_page' => (int) ceil($total / $size),
        ]);
    }

    public function sidebarData(int $id): Sidebar
    {
        $asset = Asset::with('currentAssignment.employee')->findOrFail($id);

        return Sidebar::fromModel($asset);
    }
}
