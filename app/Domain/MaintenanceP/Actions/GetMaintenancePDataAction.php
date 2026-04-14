<?php

declare(strict_types=1);

namespace App\Domain\MaintenanceP\Actions;

use App\Domain\MaintenanceP\Data\TableData;
use App\Domain\MaintenanceP\Models\MntPreventive;
use App\Domain\Shared\Data\PaginatedResult;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetMaintenancePDataAction
{
    use AsAction;

    /**
     * @param array<string, mixed>  $filters
     * @param array<string, string> $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 50): PaginatedResult
    {
        $query = MntPreventive::query()
            ->from('mnt_preventive as a')
            ->leftJoin('mnt_preventive_form as b', 'a.preventive_id', '=', 'b.id')
            ->leftJoin(
                'assets as c',
                DB::raw('c.id'),
                '=',
                DB::raw('COALESCE(NULLIF(a.asset_id, 0), b.asset_id)')
            )
            ->select([
                'a.id',
                'a.scheduled_start',
                'a.scheduled_end',
                'a.started',
                'a.attended',
                'a.closed_at',
                'a.status',
                DB::raw("COALESCE(NULLIF(a.activity,''), b.activity) AS activity_name"),
                'b.frequency',
                DB::raw("CONCAT(c.hostname,' | ',c.serial,' | ',c.sap) AS asset_full"),
                DB::raw('(a.scheduled_end::date - NOW()::date) AS days_diff'),
            ])
            ->where('a.kind', 'Machinery');

        /** @var array<string, string> $fieldMap */
        $fieldMap = [
            'id'        => 'a.id',
            'start'     => 'a.scheduled_start',
            'end'       => 'a.scheduled_end',
            'asset'     => "CONCAT(c.hostname,' | ',c.serial,' | ',c.sap)",
            'activity'  => "COALESCE(NULLIF(a.activity,''), b.activity)",
            'frequency' => 'b.frequency',
            'status'    => 'a.status',
            'days'      => '(a.scheduled_end::date - NOW()::date)',
        ];

        foreach ($filters as $field => $value) {
            if ($value === '' || $value === null) {
                continue;
            }
            if (($field === 'start' || $field === 'end') && str_contains((string) $value, ' to ')) {
                [$from, $to] = explode(' to ', (string) $value, 2);
                $query->whereBetween(DB::raw("{$fieldMap[$field]}::date"), [$from, $to]);
            } elseif (isset($fieldMap[$field])) {
                $query->where(DB::raw("CAST({$fieldMap[$field]} AS TEXT)"), 'LIKE', "%{$value}%");
            }
        }

        if ($sorts !== []) {
            foreach ($sorts as $field => $dir) {
                if (isset($fieldMap[$field])) {
                    $query->orderBy(DB::raw($fieldMap[$field]), $dir);
                }
            }
        } else {
            $query->orderByRaw(
                "CASE a.status
                    WHEN 'Open'     THEN 1
                    WHEN 'Started'  THEN 2
                    WHEN 'Attended' THEN 3
                    WHEN 'Closed'   THEN 4
                    ELSE 5
                 END, a.scheduled_end ASC"
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
