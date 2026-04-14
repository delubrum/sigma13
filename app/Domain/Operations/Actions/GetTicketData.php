<?php

declare(strict_types=1);

namespace App\Domain\Operations\Actions;

use App\Domain\Operations\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetTicketData
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $query = Ticket::query()
            ->from('tickets as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->select(['a.*', 'b.username']);

        $fieldMap = [
            'id' => 'a.id',
            'type' => 'a.kind',
            'date' => 'a.created_at',
            'user' => 'b.username',
            'facility' => 'a.facility',
            'priority' => 'a.priority',
            'description' => 'a.description',
            'status' => 'a.status',
        ];

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;
            
            if ($field === 'date' && strpos($value, ' to ') !== false) {
                [$from, $to] = explode(' to ', $value);
                $query->whereBetween(DB::raw('DATE(a.created_at)'), [$from, $to]);
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
            $query->orderBy('a.created_at', 'desc');
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        $now = new \DateTime();
        $data = collect($paginator->items())->map(function ($r) use ($now) {
            $dateClosed = $r->closed_at ? new \DateTime($r->closed_at) : $now;
            $dateCreated = new \DateTime($r->created_at);
            $interval = $dateCreated->diff($dateClosed);
            $days = (int)round((($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i) / 1440);

            return [
                'id' => $r->id,
                'type' => $r->kind,
                'date' => $r->created_at,
                'user' => $r->username,
                'facility' => $r->facility,
                'priority' => $r->priority,
                'description' => $r->description,
                'days' => $days,
                'started' => $r->started_at,
                'closed' => $r->closed_at,
                'status' => $r->status,
            ];
        });

        return [
            'data' => $data,
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
