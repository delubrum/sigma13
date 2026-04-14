<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Actions;

use App\Domain\Recruitment\Data\TableData;
use App\Domain\Recruitment\Models\Recruitment;
use App\Domain\Shared\Data\PaginatedResult;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetRecruitmentDataAction
{
    use AsAction;

    /**
     * @param array<string, mixed>  $filters
     * @param array<string, string> $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): PaginatedResult
    {
        $conversionSql = "(SELECT COUNT(id) FROM recruitment_candidates WHERE recruitment_id = a.id AND status = 'hired') / NULLIF(a.qty, 0) * 100";

        $query = Recruitment::query()
            ->from('recruitment as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->leftJoin('job_profiles as c', 'a.profile_id', '=', 'c.id')
            ->leftJoin('users as d', 'a.assignee_id', '=', 'd.id')
            ->leftJoin('hr_db as e', 'c.division_id', '=', 'e.id')
            ->select([
                'a.*',
                'b.username AS creator_name',
                'd.username AS assignee_name',
                'c.name AS profile_name',
                'e.name AS division_name',
                'e.area AS area_name',
                DB::raw("ROUND({$conversionSql}) AS conversion"),
                DB::raw("(NOW()::date - a.created_at::date) AS days_passed"),
            ]);

        /** @var array<string, string> $fieldMap */
        $fieldMap = [
            'id'       => 'a.id',
            'date'     => 'a.created_at',
            'user'     => 'b.username',
            'approver' => 'a.approver',
            'assignee' => 'd.username',
            'profile'  => 'c.name',
            'division' => 'e.name',
            'area'     => 'e.area',
            'qty'      => 'a.qty',
            'status'   => 'a.status',
        ];

        foreach ($filters as $field => $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            if ($field === 'date' && str_contains((string) $value, ' to ')) {
                [$from, $to] = explode(' to ', (string) $value, 2);
                $query->whereBetween(DB::raw('a.created_at::date'), [$from, $to]);
            } elseif (isset($fieldMap[$field])) {
                $query->where($fieldMap[$field], 'LIKE', "%{$value}%");
            }
        }

        if ($sorts !== []) {
            foreach ($sorts as $field => $dir) {
                if (isset($fieldMap[$field])) {
                    $query->orderBy($fieldMap[$field], $dir);
                }
            }
        } else {
            $query->orderBy('a.id', 'desc');
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        /** @var list<TableData> $items */
        $items = array_values(
            $paginator->getCollection()
                ->map(fn (object $row): TableData => TableData::fromModel($row))
                ->all()
        );

        return new PaginatedResult(
            items:    $items,
            lastPage: $paginator->lastPage(),
            total:    $paginator->total(),
        );
    }
}
