<?php

declare(strict_types=1);

namespace App\Domain\Operations\Actions;

use App\Domain\Operations\Models\Ppe;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetPpeDeliveries
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $query = Ppe::query()
            ->from('epp as a')
            ->leftJoin('users as b', 'a.user_id', '=', 'b.id')
            ->leftJoin('employees as c', 'a.employee_id', '=', 'c.id')
            ->leftJoin('hr_db as d', 'c.profile', '=', 'd.id')
            ->select([
                'a.id as id',
                'a.created_at as date',
                'a.name as name',
                'a.kind as type',
                'c.name as employee',
                'd.area as area',
                'b.username as user',
                'a.notes as notes',
            ]);

        $fieldMap = [
            'id' => 'a.id',
            'date' => 'a.created_at',
            'name' => 'a.name',
            'type' => 'a.kind',
            'employee' => 'c.name',
            'area' => 'd.area',
            'user' => 'b.username',
            'notes' => 'a.notes',
        ];

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;
            
            $dbField = $fieldMap[$field] ?? "a.$field";

            if ($field === 'date' && strpos((string)$value, ' to ') !== false) {
                [$from, $to] = explode(' to ', (string)$value);
                $query->whereBetween($dbField, [$from, $to]);
            } else {
                $query->where($dbField, 'LIKE', "%$value%");
            }
        }

        foreach ($sorts as $field => $dir) {
            $dbField = $fieldMap[$field] ?? "a.$field";
            $query->orderBy($dbField, $dir);
        }

        if (empty($sorts)) {
            $query->orderBy('a.id', 'desc');
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
