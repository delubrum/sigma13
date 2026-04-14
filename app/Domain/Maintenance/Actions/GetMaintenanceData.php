<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Actions;

use App\Domain\Maintenance\Models\Mnt;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetMaintenanceData
{
    use AsAction;

    public function handle(string $kind = 'Machinery', array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $query = Mnt::query()
            ->from('mnt as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->leftJoin('assets as c', 'a.asset_id', '=', 'c.id')
            ->leftJoin('users as d', 'a.assignee_id', '=', 'd.id')
            ->leftJoin(DB::raw('(SELECT mnt_id, SUM(duration) as total_time FROM mnt_items GROUP BY mnt_id) e'), 'a.id', '=', 'e.mnt_id')
            ->select([
                'a.*',
                'b.username as user_name',
                'd.username as assignee_name',
                DB::raw("CONCAT(COALESCE(c.hostname, ''), ' | ', COALESCE(c.serial, ''), ' | ', COALESCE(c.sap, '')) as asset_full"),
                DB::raw('COALESCE(e.total_time, 0) as time_sum')
            ])
            ->where('a.kind', $kind);

        $fieldMap = [
            'id' => 'a.id',
            'created_at' => 'a.created_at',
            'user' => 'b.username',
            'facility' => 'a.facility',
            'asset' => "concat(c.hostname, ' | ', c.serial, ' | ', c.sap)",
            'priority' => 'a.priority',
            'status' => 'a.status',
            'assignee' => 'd.username',
        ];

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;
            
            $dbField = $fieldMap[$field] ?? "a.$field";
            $query->where($dbField, 'LIKE', "%$value%");
        }

        foreach ($sorts as $field => $dir) {
            $dbField = $fieldMap[$field] ?? "a.$field";
            $query->orderBy($dbField, $dir);
        }

        if (empty($sorts)) {
            $query->orderByRaw("CASE a.status 
                WHEN 'Open' THEN 1 
                WHEN 'Started' THEN 2 
                WHEN 'Attended' THEN 3 
                WHEN 'Closed' THEN 4 
                WHEN 'Rated' THEN 5 
                WHEN 'Rejected' THEN 6 
                ELSE 7 END, a.created_at DESC");
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        $now = now();
        $data = collect($paginator->items())->map(function($r) use ($now) {
            $dateCreated = $r->created_at;
            $dateClosed = $r->closed_at ? \Carbon\Carbon::parse($r->closed_at) : $now;
            
            return [
                'id' => $r->id,
                'created_at' => $r->created_at->format('Y-m-d'),
                'user' => $r->user_name,
                'facility' => $r->facility,
                'asset' => $r->asset_full,
                'priority' => $r->priority,
                'status' => $r->status,
                'assignee' => $r->assignee_name,
                'days' => $dateCreated->diffInDays($dateClosed),
                'time' => (float)$r->time_sum,
                'rating' => $r->rating,
            ];
        });

        return [
            'data' => $data,
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
