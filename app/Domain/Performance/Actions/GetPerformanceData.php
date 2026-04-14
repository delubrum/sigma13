<?php

declare(strict_types=1);

namespace App\Domain\Performance\Actions;

use App\Domain\Performance\Models\PerformanceEvaluation;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetPerformanceData
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, string>  $sorts
     * @return array{data: array<int, mixed>, total: int, last_page: int}
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $query = PerformanceEvaluation::query()
            ->from('test as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->select([
                'a.id',
                'a.created_at',
                'b.username as employee',
                'a.status',
                'a.self',
                'a.leader',
                'a.peer',
                'a.score',
            ]);

        $fieldMap = [
            'id' => 'a.id',
            'created_at' => 'a.created_at',
            'employee' => 'b.username',
            'status' => 'a.status',
            'score' => 'a.score',
        ];

        foreach ($filters as $field => $value) {
            if (empty($value)) {
                continue;
            }

            $valStr = (string) (is_scalar($value) ? $value : '');

            if (isset($fieldMap[$field])) {
                $query->where($fieldMap[$field], 'LIKE', "%$valStr%");
            }
        }

        foreach ($sorts as $field => $dir) {
            if (isset($fieldMap[$field])) {
                $query->orderBy($fieldMap[$field], $dir);
            }
        }

        if ($sorts === []) {
            $query->orderBy('a.id', 'desc');
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        return [
            'data' => (array) $paginator->items(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
