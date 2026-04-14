<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Actions;

use App\Domain\Engineering\Models\Cbm;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetCbmData
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $query = Cbm::query()
            ->from('cbm as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->select([
                'a.id',
                'a.project',
                'b.username as user',
                'a.created_at as date',
                'a.total_items',
            ]);

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;
            
            if ($field === 'user') {
                $query->where('b.username', 'LIKE', "%$value%");
            } else {
                $query->where("a.$field", 'LIKE', "%$value%");
            }
        }

        foreach ($sorts as $field => $dir) {
            $dbField = ($field === 'user') ? 'b.username' : "a.$field";
            $query->orderBy($dbField, $dir);
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
