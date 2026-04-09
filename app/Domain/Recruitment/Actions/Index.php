<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Actions;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Recruitment\Data\Sidebar;
use App\Domain\Recruitment\Data\Table;
use App\Domain\Recruitment\Models\Recruitment;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use App\Domain\Shared\Data\Tabs;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class Index implements HasDetail, HasModule
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
            columns: Table::columns(),
            formFields: [
                new Field(name: 'profile_id', label: 'Perfil (Profile)', type: 'select', required: true, options: [], widget: 'slimselect', width: FieldWidth::Full), // Would be dynamically loaded usually
                new Field(name: 'approver', label: 'Approver Email', type: 'email', required: true, placeholder: 'boss@example.com'),
                new Field(name: 'city', label: 'City', required: false),
                new Field(name: 'qty', label: 'Quantity', type: 'number', required: true),
                new Field(name: 'contract', label: 'Contract Type', type: 'select', required: false, options: ['Fixed' => 'Fixed', 'Indefinite' => 'Indefinite']),
                new Field(name: 'cause', label: 'Reason/Cause', required: false),
                new Field(name: 'srange', label: 'Salary Range', required: false),
                new Field(name: 'replaces', label: 'Replaces (Who?)', required: false),
                new Field(name: 'start_date', label: 'Expected Start Date', type: 'date', required: false),
                new Field(name: 'others', label: 'Other details', type: 'textarea', required: false),
                new Field(name: 'file', label: 'Curriculum (ZIP)', type: 'file', required: false, widget: 'filepond'),
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
            'route' => 'recruitment',
            'config' => $this->config(),
        ]);
    }

    public function asData(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->integer('page', 1));
        $size = max(1, (int) $request->integer('size', 15));
        $offset = ($page - 1) * $size;

        Auth::id() ?? 0;
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
                'user' => $query->where('requestors.name', 'ilike', "%{$v}%"),
                'assignee' => $query->where('assignees.name', 'ilike', "%{$v}%"),
                'approver' => $query->where('recruitment.approver', 'ilike', "%{$v}%"),
                'profile' => $query->where('job_profiles.name', 'ilike', "%{$v}%"),
                'division' => $query->where('hr_db.name', 'ilike', "%{$v}%"),
                'area' => $query->where('hr_db.area', 'ilike', "%{$v}%"),
                'status' => $query->where('recruitment.status', $v),
                'id' => $query->where('recruitment.id', (int) $value),
                default => null,
            };
        }

        // Sorters
        /** @var list<array{field: string, dir: string}> $sorters */
        $sorters = (array) $request->input('sorters', $request->input('sort', []));
        $sort = $sorters[0] ?? ['field' => 'id', 'dir' => 'desc'];

        $sortField = match ($sort['field'] ?? '') {
            'date' => 'created_at',
            'user' => 'requestors.name',
            'assignee' => 'assignees.name',
            'approver' => 'approver',
            'profile' => 'job_profiles.name',
            'division' => 'hr_db.name',
            'status' => 'status',
            'qty' => 'qty',
            default => 'recruitment.id'
        };
        $sortDir = in_array(strtolower((string) $sort['dir']), ['asc', 'desc'], true) ? $sort['dir'] : 'desc';
        $query->orderBy($sortField, $sortDir);

        $total = $query->count();
        $rows = $query->offset($offset)->limit($size)->get();

        $data = $rows->map(fn (Recruitment $r): Table => Table::from($r));

        return response()->json([
            'data' => $data->values()->all(),
            'last_page' => (int) ceil($total / $size),
            'last_row' => $total,
        ]);
    }

    public function sidebarData(int $id): Sidebar
    {
        return Sidebar::from(Recruitment::with(['requestor', 'assignee'])->findOrFail($id));
    }
}
