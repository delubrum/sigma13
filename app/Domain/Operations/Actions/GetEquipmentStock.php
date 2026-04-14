<?php

declare(strict_types=1);

namespace App\Domain\Operations\Actions;

use App\Domain\Operations\Models\EquipmentItem;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetEquipmentStock
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $query = EquipmentItem::query()
            ->from('equipment_db as a')
            ->select([
                'a.id',
                'a.name',
                DB::raw('COALESCE((SELECT SUM(qty) FROM equipment_register WHERE item_id = a.id), 0) - COALESCE((SELECT SUM(qty) FROM equipment WHERE name = a.name), 0) as total')
            ]);

        $fieldMap = [
            'id' => 'a.id',
            'name' => 'a.name',
            'total' => 'total',
        ];

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;
            
            if ($field === 'total') {
                $query->having('total', '=', $value);
            } else {
                $dbField = $fieldMap[$field] ?? "a.$field";
                $query->where($dbField, 'LIKE', "%$value%");
            }
        }

        foreach ($sorts as $field => $dir) {
            $dbField = $fieldMap[$field] ?? "a.$field";
            $query->orderBy($dbField, $dir);
        }

        if (empty($sorts)) {
            $query->orderBy('a.name', 'asc');
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
