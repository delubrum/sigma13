<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Actions;

use App\Domain\Maintenance\Models\MaintenanceTicket;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetLocativeData
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 50): array
    {
        $query = MaintenanceTicket::query()
            ->from('mnt as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->leftJoin('assets as c', 'a.asset_id', '=', 'c.id')
            ->leftJoin('users as d', 'a.assignee_id', '=', 'd.id')
            ->leftJoin(DB::raw('(SELECT mnt_id, SUM(duration) as total_time FROM mnt_items GROUP BY mnt_id) as e'), 'a.id', '=', 'e.mnt_id')
            ->select([
                'a.*',
                'b.username',
                'd.username as assignee_name',
                'c.hostname as assetname',
                DB::raw("COALESCE(e.total_time, 0) as time_sum")
            ])
            ->where('a.kind', 'Locative');

        $fieldMap = [
            'id' => 'a.id',
            'date' => 'a.created_at',
            'user' => 'b.username',
            'facility' => 'a.facility',
            'asset' => 'c.hostname',
            'priority' => 'a.priority',
            'description' => 'a.description',
            'assignee' => 'd.username',
            'status' => 'a.status',
            'sgc' => 'a.sgc',
            'cause' => 'a.root_cause',
            'rating' => 'a.rating',
        ];

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;

            if ($field === 'date' && strpos($value, ' to ') !== false) {
                [$from, $to] = explode(' to ', $value);
                $query->whereBetween(DB::raw('a.created_at::date'), [$from, $to]);
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
            $query->orderByRaw("CASE a.status 
                WHEN 'Open' THEN 1 WHEN 'Started' THEN 2 WHEN 'Attended' THEN 3 
                WHEN 'Closed' THEN 4 WHEN 'Rated' THEN 5 WHEN 'Rejected' THEN 6 
                ELSE 7 END, a.created_at DESC");
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        $data = collect($paginator->items())->map(function ($r) {
            $dClosed = (!empty($r->closed_at)) ? new \DateTime($r->closed_at) : new \DateTime;
            $dCreated = new \DateTime($r->created_at);
            
            return [
                'id' => $r->id,
                'type' => $r->subtype,
                'date' => date('Y-m-d', strtotime($r->created_at)),
                'user' => $r->username,
                'facility' => $r->facility,
                'asset' => mb_convert_case($r->assetname ?? '', MB_CASE_TITLE, 'UTF-8'),
                'priority' => $r->priority,
                'description' => $r->description,
                'assignee' => $r->assignee_name,
                'days' => $dCreated->diff($dClosed)->days,
                'started' => $r->started_at,
                'attended' => $r->ended_at,
                'closed' => $r->closed_at,
                'time' => $r->time_sum,
                'status' => $r->status,
                'sgc' => $r->sgc,
                'cause' => $r->root_cause,
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
