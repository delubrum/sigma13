<?php

declare(strict_types=1);

namespace App\Domain\Operations\Actions;

use App\Domain\Operations\Models\Evaluation;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetEvaluationData
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $query = Evaluation::query()
            ->from('suppliers_evaluation as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->leftJoin(DB::raw("JSON_TABLE(IFNULL(a.answers,'{}'), '$.*' COLUMNS (val VARCHAR(1) PATH '$')) as jt"), DB::raw('TRUE'), '=', DB::raw('TRUE'))
            ->select([
                'a.*',
                'a.created_at as date',
                'b.username as user',
                DB::raw("ROUND(((SUM(jt.val = '1') + SUM(jt.val = '2') * 0.5) / 12) * 100) AS result")
            ])
            ->groupBy('a.id');

        $fieldMap = [
            'id' => 'a.id',
            'date' => 'a.created_at',
            'nit' => 'a.nit',
            'supplier' => 'a.supplier',
            'user' => 'b.username',
            'type' => 'a.kind',
            'result' => 'result',
        ];

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;

            if (isset($fieldMap[$field])) {
                if ($field === 'result') {
                    $query->having('result', 'LIKE', "%$value%");
                } else {
                    $query->where($fieldMap[$field], 'LIKE', "%$value%");
                }
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

        return [
            'data' => $paginator->items(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
