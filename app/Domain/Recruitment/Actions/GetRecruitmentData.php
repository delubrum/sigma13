<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Actions;

use App\Domain\Recruitment\Models\Recruitment;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetRecruitmentData
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $conversionSql = "(SELECT COUNT(id) FROM recruitment_candidates WHERE recruitment_id = a.id AND status = 'hired') / a.qty * 100";

        $query = Recruitment::query()
            ->from('recruitment as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->leftJoin('job_profiles as c', 'a.profile_id', '=', 'c.id')
            ->leftJoin('users as d', 'a.assignee_id', '=', 'd.id')
            ->leftJoin('hr_db as e', 'c.division_id', '=', 'e.id')
            ->select([
                'a.*',
                'b.username as creator_name',
                'd.username as assignee_name',
                'c.name as profile_name',
                'e.name as division_name',
                'e.area as area_name',
                DB::raw("ROUND($conversionSql) as conversion"),
                DB::raw("DATEDIFF(NOW(), a.created_at) as days_passed")
            ]);

        $fieldMap = [
            'id' => 'a.id',
            'date' => 'a.created_at',
            'user' => 'b.username',
            'approver' => 'a.approver',
            'assignee' => 'd.username',
            'profile' => 'c.name',
            'division' => 'e.name',
            'area' => 'e.area',
            'qty' => 'a.qty',
            'status' => 'a.status',
        ];

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;

            if ($field === 'date' && strpos($value, ' to ') !== false) {
                [$from, $to] = explode(' to ', $value);
                $query->whereBetween(DB::raw('a.created_at::date'), [$from, $to]);
            } elseif (isset($fieldMap[$field])) {
                $query->where($fieldMap[$field], 'LIKE', "%$value%");
            }
        }

        foreach ($sorts as $field => $dir) {
            if (isset($fieldMap[$field])) {
                $query->orderBy($fieldMap[$field], $dir);
            }
        }

        if (empty($sorts)) {
            $query->orderBy('a.id', 'desc');
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        $data = collect($paginator->items())->map(fn($r) => [
            'id' => $r->id,
            'date' => $r->created_at,
            'user' => $r->creator_name,
            'approver' => $r->approver,
            'assignee' => $r->assignee_name,
            'profile' => $r->profile_name,
            'division' => $r->division_name,
            'area' => $r->area_name,
            'qty' => $r->qty,
            'conversion' => $r->conversion,
            'days' => $r->days_passed,
            'status' => $r->status,
        ]);

        return [
            'data' => $data,
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
