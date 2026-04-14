<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Actions;

use App\Domain\Maintenance\Data\TableData;
use App\Domain\Maintenance\Models\Mnt;
use App\Domain\Shared\Data\PaginatedResult;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetMaintenanceDataAction
{
    use AsAction;

    /**
     * @param array<string, mixed>  $filters
     * @param array<string, string> $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(string $kind = 'Machinery', array $filters = [], array $sorts = [], int $page = 1, int $size = 50): PaginatedResult
    {
        $query = Mnt::query()
            ->from('mnt as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->leftJoin('assets as c', 'a.asset_id', '=', 'c.id')
            ->leftJoin('users as d', 'a.assignee_id', '=', 'd.id')
            ->leftJoin(
                DB::raw('(SELECT mnt_id, SUM(duration) AS total_time FROM mnt_items GROUP BY mnt_id) e'),
                'a.id', '=', 'e.mnt_id'
            )
            ->select([
                'a.*',
                'b.username AS user_name',
                'd.username AS assignee_name',
                DB::raw("CONCAT(COALESCE(c.hostname,''),' | ',COALESCE(c.serial,''),' | ',COALESCE(c.sap,'')) AS asset_full"),
                DB::raw('COALESCE(e.total_time, 0) AS time_sum'),
            ])
            ->where('a.kind', $kind);

        /** @var array<string, string> $fieldMap */
        $fieldMap = [
            'id'       => 'a.id',
            'date'     => 'a.created_at',
            'user'     => 'b.username',
            'facility' => 'a.facility',
            'asset'    => "CONCAT(c.hostname,' | ',c.serial,' | ',c.sap)",
            'priority' => 'a.priority',
            'status'   => 'a.status',
            'assignee' => 'd.username',
        ];

        foreach ($filters as $field => $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            $dbField = $fieldMap[$field] ?? "a.{$field}";
            $query->where($dbField, 'LIKE', "%{$value}%");
        }

        if ($sorts !== []) {
            foreach ($sorts as $field => $dir) {
                $dbField = $fieldMap[$field] ?? "a.{$field}";
                $query->orderBy($dbField, $dir);
            }
        } else {
            $query->orderByRaw(
                "CASE a.status
                    WHEN 'Open'     THEN 1
                    WHEN 'Started'  THEN 2
                    WHEN 'Attended' THEN 3
                    WHEN 'Closed'   THEN 4
                    WHEN 'Rated'    THEN 5
                    WHEN 'Rejected' THEN 6
                    ELSE 7
                 END, a.created_at DESC"
            );
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
