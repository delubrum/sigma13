<?php

declare(strict_types=1);

namespace App\Domain\Operations\Actions;

use App\Domain\Operations\Models\PrintWo;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetPrintData
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $query = PrintWo::query()
            ->from('wo as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->select(['a.*', 'b.username']);

        $fieldMap = [
            'id' => 'a.code',
            'project' => 'a.project',
            'user' => 'b.username',
            'date' => 'a.created_at',
            'es' => 'a.es_id',
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
            $query->orderBy('a.created_at', 'desc');
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        $data = collect($paginator->items())->map(function ($r) {
            return [
                'id' => $r->code,
                'project' => $r->project,
                'user' => $r->username,
                'date' => $r->created_at,
                'es' => $r->es_id,
            ];
        });

        return [
            'data' => $data,
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
