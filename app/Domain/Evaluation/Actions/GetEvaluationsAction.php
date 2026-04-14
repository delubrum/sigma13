<?php

declare(strict_types=1);

namespace App\Domain\Evaluation\Actions;

use App\Domain\Evaluation\Data\EvaluationsTableData;
use App\Domain\Shared\Data\PaginatedResult;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetEvaluationsAction
{
    use AsAction;

    public function handle(int $page = 1, int $size = 25, ?array $filters = []): PaginatedResult
    {
        $offset = ($page - 1) * $size;

        $query = DB::table('suppliers_evaluation as a')
            ->leftJoin('users as b', 'b.id', '=', 'a.user_id')
            ->select([
                'a.id',
                'a.created_at as date',
                'b.name as user',
                'a.nit',
                'a.supplier',
                'a.kind as type',
                DB::raw("(
                    SELECT ROUND(((
                        COUNT(*) FILTER (WHERE value = '1') + 
                        COUNT(*) FILTER (WHERE value = '2') * 0.5
                    ) / 12.0) * 100)
                    FROM jsonb_each_text(COALESCE(NULLIF(a.answers, '')::jsonb, '{}'::jsonb))
                ) as result")
            ]);

        // Filtering
        if (! empty($filters)) {
            $fieldMap = [
                'date' => 'a.created_at',
                'user' => 'b.name',
                'nit' => 'a.nit',
                'supplier' => 'a.supplier',
                'type' => 'a.kind',
            ];

            foreach ($filters as $f) {
                if ($dbField = ($fieldMap[$f['field']] ?? null)) {
                    $query->where($dbField, 'like', '%' . $f['value'] . '%');
                }
            }
        }

        $totalCount = DB::table('suppliers_evaluation')->count(); 
        
        $rows = $query->orderBy('a.created_at', 'desc')
            ->offset($offset)
            ->limit($size)
            ->get();

        $items = $rows->map(fn($row) => EvaluationsTableData::from([
            'id' => (int) $row->id,
            'date' => (string) $row->date,
            'user' => (string) $row->user,
            'nit' => (string) $row->nit,
            'supplier' => (string) $row->supplier,
            'type' => (string) $row->type,
            'result' => (float) $row->result,
        ]))->toArray();

        return new PaginatedResult(
            items: $items,
            lastPage: (int) ceil($totalCount / $size),
            total: $totalCount
        );
    }
}
