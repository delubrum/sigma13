<?php

declare(strict_types=1);

namespace App\Domain\Operations\Actions;

use App\Domain\Operations\Models\Improvement;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetImprovementData
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $query = Improvement::query()
            ->from('improvement as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->leftJoin('users as c', 'a.responsible_id', '=', 'c.id')
            ->select([
                'a.*',
                'b.username as creator_name',
                'c.username as responsible_name',
                DB::raw("SUBSTRING_INDEX(a.process, ' || ', 1) AS code_prefix"),
                DB::raw("(SELECT COUNT(*) FROM improvement AS t2 
                         WHERE SUBSTRING_INDEX(t2.process, ' || ', 1) = SUBSTRING_INDEX(a.process, ' || ', 1) 
                         AND t2.id <= a.id) AS seq_number")
            ]);

        $fieldMap = [
            'id' => 'a.id',
            'occurrence_date' => 'a.occurrence_date',
            'creator_name' => 'b.username',
            'responsible_name' => 'c.username',
            'kind' => 'a.kind',
            'source' => 'a.source',
            'process' => 'a.process',
            'perspective' => 'a.perspective',
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
            $query->orderBy('a.id', 'desc');
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        $data = collect($paginator->items())->map(fn($r) => [
            'id' => $r->id,
            'occurrence_date' => $r->occurrence_date,
            'code' => "{$r->code_prefix}-{$r->seq_number}",
            'creator_name' => $r->creator_name,
            'responsible_name' => $r->responsible_name,
            'kind' => $r->kind,
            'source' => $r->source,
            'process' => $r->process,
            'perspective' => $r->perspective,
            'status' => $r->status,
        ]);

        return [
            'data' => $data,
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
