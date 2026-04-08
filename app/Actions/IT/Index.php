<?php

declare(strict_types=1);

namespace App\Actions\IT;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Data\IT\Sidebar;
use App\Data\IT\Table;
use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Data\Shared\FieldWidth;
use App\Data\Shared\Tabs;
use App\Models\It;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\Data;

final class Index implements HasModule, HasDetail
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
            columns: [
                ['title' => 'ID', 'field' => 'id', 'width' => 60, 'sorter' => 'number', 'headerHozAlign' => 'center', 'headerFilter' => 'input'],
                ['title' => 'Date', 'field' => 'date', 'headerHozAlign' => 'center', 'headerFilter' => 'input'],
                ['title' => 'User', 'field' => 'user', 'headerHozAlign' => 'left', 'headerFilter' => 'input'],
                ['title' => 'Facility', 'field' => 'facility', 'headerHozAlign' => 'left', 'headerFilter' => 'input'],
                ['title' => 'Asset', 'field' => 'asset', 'headerHozAlign' => 'left', 'headerFilter' => 'input'],
                ['title' => 'Priority', 'field' => 'priority', 'headerHozAlign' => 'left', 'headerFilter' => 'input'],
                [
                    'title' => 'Status', 'field' => 'status', 'headerHozAlign' => 'center', 'hozAlign' => 'center',
                    'headerFilter' => 'list',
                    'headerFilterParams' => ['values' => ['Open' => 'Open', 'Started' => 'Started', 'Attended' => 'Attended', 'Closed' => 'Closed', 'Rated' => 'Rated', 'Rejected' => 'Rejected'], 'clearable' => true],
                ],
                ['title' => 'Description', 'field' => 'description', 'formatter' => 'textarea', 'headerHozAlign' => 'left', 'headerFilter' => 'input'],
                ['title' => 'Assignee', 'field' => 'assignee', 'headerHozAlign' => 'left', 'headerFilter' => 'input'],
                ['title' => 'Days', 'field' => 'days', 'sorter' => 'number', 'width' => 80, 'headerHozAlign' => 'center'],
                ['title' => 'Started', 'field' => 'started_at', 'headerHozAlign' => 'center', 'headerFilter' => 'input'],
                ['title' => 'Attended', 'field' => 'ended_at', 'headerHozAlign' => 'center', 'headerFilter' => 'input'],
                ['title' => 'Closed', 'field' => 'closed_at', 'headerHozAlign' => 'center', 'headerFilter' => 'input'],
                ['title' => 'Hours', 'field' => 'time', 'sorter' => 'number', 'width' => 80, 'headerHozAlign' => 'center'],
                ['title' => 'SGC', 'field' => 'sgc', 'headerHozAlign' => 'left', 'headerFilter' => 'input'],
                ['title' => 'Rating', 'field' => 'rating', 'sorter' => 'number', 'width' => 80, 'headerHozAlign' => 'center', 'headerFilter' => 'number'],
            ],
            formFields: [
                new Field(name: 'facility', label: 'Sede', required: true, options: ['ESM1' => 'ESM1', 'ESM2' => 'ESM2', 'ESM3' => 'ESM3', 'Medellín' => 'Medellín'], widget: 'slimselect', width: FieldWidth::Half),
                new Field(name: 'kind', label: 'Tipo', required: true, options: ['Equipment/Accessories' => 'Equipment / Accessories', 'Licenses' => 'Licenses', 'Permissions' => 'Permissions'], widget: 'slimselect', width: FieldWidth::Half),
                new Field(name: 'priority', label: 'Prioridad', required: true, type: 'select', options: ['High' => 'Right Now. Locked', 'Medium' => 'Today. Need Attention', 'Low' => 'Tomorrow. I Can Wait'], widget: 'slimselect', width: FieldWidth::Half),
                new Field(name: 'description', label: 'Descripción', required: true, type: 'textarea', placeholder: 'Describe el problema...'),
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
            'route'  => 'it',
            'config' => $this->config(),
        ]);
    }

    public function asData(Request $request): JsonResponse
    {
        $page   = max(1, (int) $request->integer('page', 1));
        $size   = max(1, (int) $request->integer('size', 50));
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
        $filters = $request->input('filters', $request->input('filter', []));
        if (is_array($filters)) {
            foreach ($filters as $f) {
                $field = (string) ($f['field'] ?? '');
                $value = $f['value'] ?? null;
                if ($value === null || $value === '') {
                    continue;
                }
                $v = (string) $value;
                match ($field) {
                    'description', 'priority', 'facility', 'sgc' => $query->where("it.{$field}", 'ilike', "%{$v}%"),
                    'status'   => $query->where('it.status', $v),
                    'user'     => $query->where('requestors.name', 'ilike', "%{$v}%"),
                    'assignee' => $query->where('assignees.name', 'ilike', "%{$v}%"),
                    'asset'    => $query->where(DB::raw("CONCAT(assets.hostname, ' | ', assets.serial)"), 'ilike', "%{$v}%"),
                    'id'       => $query->where('it.id', (int) $value),
                    'rating'   => $query->where('it.rating', (int) $value),
                    default    => null,
                };
            }
        }

        // Sorters
        $sortRaw = $request->input('sorters', $request->input('sort', []));
        /** @var array{field?: string, dir?: string} $sort */
        $sort = is_array($sortRaw) && isset($sortRaw[0]) && is_array($sortRaw[0]) ? $sortRaw[0] : [];
        $sortField = match ($sort['field'] ?? '') {
            'status'   => 'status',
            'priority' => 'priority',
            'facility' => 'facility',
            'date'     => 'created_at',
            'user'     => 'requestors.name',
            'assignee' => 'assignees.name',
            'asset'    => 'assets.hostname',
            default    => 'it.id'
        };
        $sortDir = in_array(strtolower($sort['dir'] ?? ''), ['asc', 'desc'], true) ? $sort['dir'] : 'desc';

        if (($sort['field'] ?? '') === 'status') {
            $query->orderByRaw("CASE it.status
                WHEN 'Open'     THEN 1
                WHEN 'Started'  THEN 2
                WHEN 'Attended' THEN 3
                WHEN 'Closed'   THEN 4
                WHEN 'Rated'    THEN 5
                WHEN 'Rejected' THEN 6
                ELSE 7 END " . $sortDir);
        } else {
            $query->orderBy($sortField, $sortDir);
        }

        $total = $query->count();
        $rows  = $query->offset($offset)->limit($size)->get();

        $data = $rows->map(fn (It $r): Table => Table::fromModel($r));

        return response()->json([
            'data'      => $data->values()->all(),
            'last_page' => (int) ceil($total / $size),
        ]);
    }

    /** Requerido por HasDetail — retorna Data mínimo para sidebar del ticket */
    public function sidebarData(int $id): Data
    {
        return Sidebar::fromModel(It::with(['requestor', 'assignee'])->findOrFail($id));
    }
}
