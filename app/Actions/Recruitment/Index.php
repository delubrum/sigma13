<?php

declare(strict_types=1);

namespace App\Actions\Recruitment;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Data\Recruitment\Sidebar;
use App\Data\Recruitment\Table;
use App\Data\Shared\Config;
use App\Data\Shared\Field;
use App\Data\Shared\FieldWidth;
use App\Data\Shared\Tabs;
use App\Models\Recruitment;
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
            title: 'Recruitment',
            subtitle: 'HR / Recruitment',
            icon: 'ri-user-search-line',
            newButtonLabel: 'New Recruitment',
            showKpi: true,
            columns: [
                ['title' => 'ID', 'field' => 'id', 'width' => 60, 'sorter' => 'number', 'headerHozAlign' => 'center', 'headerFilter' => 'input'],
                ['title' => 'Date', 'field' => 'date', 'headerHozAlign' => 'center', 'headerFilter' => 'input'],
                ['title' => 'Creator', 'field' => 'user', 'headerHozAlign' => 'left', 'headerFilter' => 'input'],
                ['title' => 'Approver', 'field' => 'approver', 'headerHozAlign' => 'left', 'headerFilter' => 'input'],
                ['title' => 'Assignee', 'field' => 'assignee', 'headerHozAlign' => 'left', 'headerFilter' => 'input'],
                ['title' => 'Profile', 'field' => 'profile', 'headerHozAlign' => 'left', 'headerFilter' => 'input'],
                ['title' => 'Division', 'field' => 'division', 'headerHozAlign' => 'center', 'headerFilter' => 'input'],
                ['title' => 'Area', 'field' => 'area', 'headerHozAlign' => 'center', 'headerFilter' => 'input'],
                ['title' => 'Qty', 'field' => 'qty', 'headerHozAlign' => 'center', 'headerFilter' => 'input'],
                // Progress formatter can be added in frontend using JS or custom formatters config. Let's send raw value for now or basic progress bar format.
                ['title' => 'Conversion', 'field' => 'conversion', 'headerHozAlign' => 'center', 'headerFilter' => 'input', 'formatter' => 'progress'],
                ['title' => 'Days', 'field' => 'days', 'sorter' => 'number', 'width' => 80, 'headerHozAlign' => 'center'],
                [
                    'title' => 'Status', 'field' => 'status', 'headerHozAlign' => 'center', 'hozAlign' => 'center',
                    'headerFilter' => 'list',
                    'headerFilterParams' => ['values' => ['approved' => 'Approved', 'approval' => 'Approval', 'closed' => 'Closed'], 'clearable' => true],
                ],
            ],
            formFields: [
                new Field(name: 'profile_id', label: 'Perfil (Profile)', required: true, type: 'select', options: [], widget: 'slimselect', width: FieldWidth::Full), // Would be dynamically loaded usually
                new Field(name: 'approver', label: 'Approver Email', required: true, type: 'email', placeholder: 'boss@example.com'),
                new Field(name: 'city', label: 'City', required: false),
                new Field(name: 'qty', label: 'Quantity', required: true, type: 'number'),
                new Field(name: 'contract', label: 'Contract Type', required: false, type: 'select', options: ['Fixed' => 'Fixed', 'Indefinite' => 'Indefinite']),
                new Field(name: 'cause', label: 'Reason/Cause', required: false),
                new Field(name: 'srange', label: 'Salary Range', required: false),
                new Field(name: 'replaces', label: 'Replaces (Who?)', required: false),
                new Field(name: 'start_date', label: 'Expected Start Date', required: false, type: 'date'),
                new Field(name: 'others', label: 'Other details', required: false, type: 'textarea'),
                new Field(name: 'file', label: 'Curriculum (ZIP)', required: false, type: 'file', widget: 'filepond'),
            ],
            tabs: [
                new Tabs(key: 'detail', label: 'Detail', icon: 'ri-information-line', route: 'recruitment.detail', default: true),
                new Tabs(key: 'candidates', label: 'Candidates', icon: 'ri-group-line', route: 'recruitment.candidates'),
            ],
        );
    }

    public function asController(Request $request): View
    {
        return view('components.index', [
            'route'  => 'recruitment',
            'config' => $this->config(),
        ]);
    }

    public function asData(Request $request): JsonResponse
    {
        $page   = max(1, (int) $request->integer('page', 1));
        $size   = max(1, (int) $request->integer('size', 15));
        $offset = ($page - 1) * $size;

        $userId = \Illuminate\Support\Facades\Auth::id() ?? 0;
        // In the original, there was some logic: if($userId == 505) assignee, else if(!isAdmin) user_id

        $query = Recruitment::query()
            ->select([
                'recruitment.id',
                'recruitment.created_at',
                'recruitment.approver',
                'recruitment.status',
                'recruitment.qty',
                DB::raw('"requestors"."name" as user_name'),
                DB::raw('"assignees"."name" as assignee_name'),
                DB::raw('"job_profiles"."name" as profile_name'),
                DB::raw('"hr_db"."name" as division_name'),
                DB::raw('"hr_db"."area" as area_name'),
                DB::raw('COALESCE((SELECT COUNT(id) FROM recruitment_candidates WHERE recruitment_candidates.recruitment_id = recruitment.id AND recruitment_candidates.status = \'hired\'), 0) as hired_count'),
                DB::raw('(recruitment.complexity - EXTRACT(DAY FROM (CURRENT_TIMESTAMP - recruitment.created_at))) as days_remaining'),
            ])
            ->leftJoin('users as requestors', 'recruitment.user_id', '=', 'requestors.id')
            ->leftJoin('users as assignees', 'recruitment.assignee_id', '=', 'assignees.id')
            ->leftJoin('job_profiles', 'recruitment.profile_id', '=', 'job_profiles.id')
            ->leftJoin('hr_db', 'job_profiles.division_id', '=', 'hr_db.id');

        // Filters (Search and filtering)
        $filters = $request->input('filters', $request->input('filter', []));
        if (is_array($filters)) {
            foreach ($filters as $f) {
                if (!is_array($f)) {
                    continue;
                }
                $fieldVal = $f['field'] ?? '';
                $field = is_scalar($fieldVal) ? (string) $fieldVal : '';
                $value = $f['value'] ?? null;
                if ($value === null || $value === '' || !is_scalar($value)) {
                    continue;
                }
                $v = (string) $value;
                match ($field) {
                    'user'     => $query->where('requestors.name', 'ilike', "%{$v}%"),
                    'assignee' => $query->where('assignees.name', 'ilike', "%{$v}%"),
                    'approver' => $query->where('recruitment.approver', 'ilike', "%{$v}%"),
                    'profile'  => $query->where('job_profiles.name', 'ilike', "%{$v}%"),
                    'division' => $query->where('hr_db.name', 'ilike', "%{$v}%"),
                    'area'     => $query->where('hr_db.area', 'ilike', "%{$v}%"),
                    'status'   => $query->where('recruitment.status', $v),
                    'id'       => $query->where('recruitment.id', (int) $value),
                    default    => null,
                };
            }
        }

        // Sorters
        $sortRaw = $request->input('sorters', $request->input('sort', []));
        /** @var array{field?: string, dir?: string} $sort */
        $sort = is_array($sortRaw) && isset($sortRaw[0]) && is_array($sortRaw[0]) ? $sortRaw[0] : [];
        $sortField = match ($sort['field'] ?? '') {
            'date'     => 'created_at',
            'user'     => 'requestors.name',
            'assignee' => 'assignees.name',
            'approver' => 'approver',
            'profile'  => 'job_profiles.name',
            'division' => 'hr_db.name',
            'status'   => 'status',
            'qty'      => 'qty',
            default    => 'recruitment.id'
        };
        $sortDir = in_array(strtolower($sort['dir'] ?? ''), ['asc', 'desc'], true) ? $sort['dir'] : 'desc';
        $query->orderBy($sortField, $sortDir);

        $total = $query->count();
        $rows  = $query->offset($offset)->limit($size)->get();

        $data = $rows->map(fn (Recruitment $r): Table => Table::fromModel($r));

        return response()->json([
            'data'      => $data->values()->all(),
            'last_page' => (int) ceil($total / $size),
            'last_row'  => $total,
        ]);
    }

    public function sidebarData(int $id): Data
    {
        return Sidebar::fromModel(Recruitment::with(['requestor', 'assignee'])->findOrFail($id));
    }
}
