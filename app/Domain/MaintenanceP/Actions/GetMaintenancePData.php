<?php

declare(strict_types=1);

namespace App\Domain\MaintenanceP\Actions;

use App\Domain\MaintenanceP\Models\MntPreventive;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetMaintenancePData
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 50): array
    {
        $query = MntPreventive::query()
            ->from('mnt_preventive as a')
            ->leftJoin('mnt_preventive_form as b', 'a.preventive_id', '=', 'b.id')
            ->leftJoin('assets as c', DB::raw('c.id'), '=', DB::raw('COALESCE(NULLIF(a.asset_id, 0), b.asset_id)'))
            ->select([
                'a.id',
                'a.scheduled_start',
                'a.scheduled_end',
                'a.started',
                'a.attended',
                'a.closed_at',
                'a.status',
                DB::raw("COALESCE(NULLIF(a.activity, ''), b.activity) AS activity_name"),
                'b.frequency',
                DB::raw("concat(c.hostname, ' | ', c.serial, ' | ', c.sap) as asset_full"),
                DB::raw("DATEDIFF(a.scheduled_end, NOW()) as days_diff")
            ])
            ->where('a.kind', 'Machinery');

        $fieldMap = [
            'id' => 'a.id',
            'start' => 'a.scheduled_start',
            'end' => 'a.scheduled_end',
            'asset' => "concat(c.hostname, ' | ', c.serial, ' | ', c.sap)",
            'activity' => "COALESCE(NULLIF(a.activity, ''), b.activity)",
            'frequency' => 'b.frequency',
            'status' => 'a.status',
            'days' => 'DATEDIFF(a.scheduled_end, NOW())',
        ];

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;

            if (($field === 'start' || $field === 'end') && strpos($value, ' to ') !== false) {
                [$from, $to] = explode(' to ', $value);
                $query->whereBetween(DB::raw("DATE({$fieldMap[$field]})"), [$from, $to]);
            } elseif (isset($fieldMap[$field])) {
                $query->where(DB::raw("CAST({$fieldMap[$field]} AS TEXT)"), 'LIKE', "%$value%");
            }
        }

        if (!empty($sorts)) {
            foreach ($sorts as $field => $dir) {
                if (isset($fieldMap[$field])) {
                    $query->orderBy(DB::raw($fieldMap[$field]), $dir);
                }
            }
        } else {
            $query->orderByRaw("FIELD(a.status, 'Open', 'Started', 'Attended', 'Closed'), a.scheduled_end ASC");
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        $data = collect($paginator->items())->map(function ($r) {
            $days = (int) $r->days_diff;
            $color = ($days >= 0) ? 'text-green-500' : 'text-red-500';
            if (!empty($r->closed_at) && $r->closed_at != '0000-00-00 00:00:00') {
                $color = 'text-gray-500';
            }

            return [
                'id' => $r->id,
                'start' => $r->scheduled_start,
                'end' => $r->scheduled_end,
                'asset' => $r->asset_full,
                'activity' => $r->activity_name,
                'frequency' => $r->frequency,
                'status' => $r->status,
                'days' => "<span class='font-bold $color'>$days</span>",
                'started' => $r->started,
                'attended' => $r->attended,
                'closed' => $r->closed_at,
            ];
        });

        return [
            'data' => $data,
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
