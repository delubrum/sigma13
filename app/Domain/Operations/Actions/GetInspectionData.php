<?php

declare(strict_types=1);

namespace App\Domain\Operations\Actions;

use App\Domain\Operations\Models\Inspection;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetInspectionData
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $query = Inspection::query()
            ->from('inspections as a')
            ->leftJoin('inspection_automations as b', 'a.automation_id', '=', 'b.id')
            ->leftJoin('assets as c', 'b.asset_id', '=', 'c.id')
            ->select([
                'a.*',
                'b.frequency',
                DB::raw("CONCAT(COALESCE(c.hostname, ''), ' | ', COALESCE(c.serial, ''), ' | ', COALESCE(c.sap, '')) as asset_full"),
                DB::raw('DATEDIFF(a.due_date, CURDATE()) as days_diff')
            ]);

        $fieldMap = [
            'id' => 'a.id',
            'created_at' => 'a.created_at',
            'due_date' => 'a.due_date',
            'asset' => "concat(c.hostname, ' | ', c.serial, ' | ', c.sap)",
            'frequency' => 'b.frequency',
            'status' => 'a.status',
        ];

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;

            if (isset($fieldMap[$field])) {
                $query->where($fieldMap[$field], 'LIKE', "%$value%");
            }
        }

        foreach ($sorts as $field => $dir) {
            if (isset($fieldMap[$field])) {
                $query->orderBy($fieldMap[$field], $dir);
            }
        }

        if (empty($sorts)) {
            $query->orderByRaw("FIELD(a.status, 'Open', 'Started', 'Closed'), a.due_date ASC");
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        $data = collect($paginator->items())->map(function($r) {
            $days = (int) $r->days_diff;
            $color = ($days >= 0) ? 'text-green-500' : 'text-red-500';
            if (!empty($r->closed_at)) {
                $color = 'text-gray-500';
            }

            return [
                'id' => $r->id,
                'created_at' => $r->created_at->format('Y-m-d H:i'),
                'due_date' => $r->due_date,
                'asset' => $r->asset_full,
                'frequency' => $r->frequency,
                'status' => $r->status,
                'started' => $r->started_at,
                'closed' => $r->closed_at,
                'days' => "<span class='font-bold $color'>$days</span>",
            ];
        });

        return [
            'data' => $data,
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
