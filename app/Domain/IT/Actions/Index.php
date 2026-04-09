<?php

declare(strict_types=1);

namespace App\Domain\IT\Actions;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\IT\Data\Sidebar;
use App\Domain\IT\Data\Table;
use App\Domain\IT\Models\It;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use App\Domain\Shared\Data\Tabs;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\Data;

final class Index implements HasDetail, HasModule
{
    use AsAction;

    public function config(): Config
    {
        return new Config(
            title: 'Service Desk',
            subtitle: 'IT / Infrastructure',
            icon: 'ri-computer-line',
            newButtonLabel: 'New Ticket',
            showKpi: true,
            columns: Table::columns(),
            formFields: [
                new Field(name: 'facility', label: 'Sede', required: true, options: ['ESM1' => 'ESM1', 'ESM2' => 'ESM2', 'ESM3' => 'ESM3', 'Medellín' => 'Medellín'], widget: 'slimselect', width: FieldWidth::Half),
                new Field(name: 'kind', label: 'Tipo', required: true, options: ['Equipment/Accessories' => 'Equipment / Accessories', 'Licenses' => 'Licenses', 'Permissions' => 'Permissions'], widget: 'slimselect', width: FieldWidth::Half),
                new Field(name: 'priority', label: 'Prioridad', type: 'select', required: true, options: ['High' => 'Right Now. Locked', 'Medium' => 'Today. Need Attention', 'Low' => 'Tomorrow. I Can Wait'], widget: 'slimselect', width: FieldWidth::Half),
                new Field(name: 'description', label: 'Descripción', type: 'textarea', required: true, placeholder: 'Describe el problema...'),
                new Field(name: 'files', label: 'Evidencia (Foto)', type: 'file', widget: 'filepond'),
            ],
            tabs: [
                new Tabs(key: 'detail', label: 'Detalle', icon: 'ri-information-line', route: 'it.detail', default: true),
            ],
        );
    }

    public function asController(Request $request): View
    {
        return view('components.index', [
            'route' => 'it',
            'config' => $this->config(),
        ]);
    }

    public function asData(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->integer('page', 1));
        $size = max(1, (int) $request->integer('size', 50));
        $offset = ($page - 1) * $size;

        $query = It::query()
            ->select([
                'it.id',
                'it.facility',
                'it.priority',
                'it.description',
                'it.status',
                'it.sgc',
                'it.rating',
                'it.started_at',
                'it.ended_at',
                'it.closed_at',
                'it.created_at',
                DB::raw('"requestors"."name" as requestor_name'),
                DB::raw('"assignees"."name" as assignee_name'),
                DB::raw("COALESCE(CONCAT(assets.hostname, ' | ', assets.serial), '') as asset_hostname"),
                DB::raw('COALESCE((SELECT SUM(duration) FROM it_items WHERE it_items.it_id = it.id), 0) as time_sum'),
                DB::raw('EXTRACT(DAY FROM (COALESCE(it.closed_at, CURRENT_TIMESTAMP) - it.created_at)) as days_count'),
            ])
            ->leftJoin('users as requestors', 'it.user_id', '=', 'requestors.id')
            ->leftJoin('assets', 'it.asset_id', '=', 'assets.id')
            ->leftJoin('users as assignees', 'it.assignee_id', '=', 'assignees.id');

        // Filters
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
                'description', 'priority', 'facility', 'sgc' => $query->where("it.{$field}", 'ilike', "%{$v}%"),
                'status' => $query->where('it.status', $v),
                'user' => $query->where('requestors.name', 'ilike', "%{$v}%"),
                'assignee' => $query->where('assignees.name', 'ilike', "%{$v}%"),
                'asset' => $query->where(DB::raw("CONCAT(assets.hostname, ' | ', assets.serial)"), 'ilike', "%{$v}%"),
                'id' => $query->where('it.id', (int) $value),
                'rating' => $query->where('it.rating', (int) $value),
                default => null,
            };
        }

        // Sorters
        /** @var list<array{field: string, dir: string}> $sorters */
        $sorters = (array) $request->input('sorters', $request->input('sort', []));
        $sort = $sorters[0] ?? ['field' => 'id', 'dir' => 'desc'];

        $sortField = match ($sort['field'] ?? '') {
            'status' => 'status',
            'priority' => 'priority',
            'facility' => 'facility',
            'date' => 'created_at',
            'user' => 'requestors.name',
            'assignee' => 'assignees.name',
            'asset' => 'assets.hostname',
            default => 'it.id'
        };
        $dir = strtolower((string) ($sort['dir'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';

        if (($sort['field'] ?? '') === 'status') {
            if ($dir === 'asc') {
                $query->orderByRaw("CASE it.status
                    WHEN 'Open'     THEN 1
                    WHEN 'Started'  THEN 2
                    WHEN 'Attended' THEN 3
                    WHEN 'Closed'   THEN 4
                    WHEN 'Rated'    THEN 5
                    WHEN 'Rejected' THEN 6
                    ELSE 7 END asc");
            } else {
                $query->orderByRaw("CASE it.status
                    WHEN 'Open'     THEN 1
                    WHEN 'Started'  THEN 2
                    WHEN 'Attended' THEN 3
                    WHEN 'Closed'   THEN 4
                    WHEN 'Rated'    THEN 5
                    WHEN 'Rejected' THEN 6
                    ELSE 7 END desc");
            }
        } else {
            $query->orderBy($sortField, $dir);
        }
        $total = $query->count();
        $rows = $query->offset($offset)->limit($size)->get();

        $data = $rows->map(fn (It $r): Table => Table::from($r));

        return response()->json([
            'data' => $data->values()->all(),
            'last_page' => (int) ceil($total / $size),
        ]);
    }

    /** Requerido por HasDetail — retorna Data mínimo para sidebar del ticket */
    public function sidebarData(int $id): Sidebar
    {
        return Sidebar::from(It::with(['requestor', 'assignee'])->findOrFail($id));
    }
}
