<?php

declare(strict_types=1);

namespace App\Domain\Operations\Actions;

use App\Domain\Operations\Models\EquipmentItem;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetEquipmentData
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $query = EquipmentItem::query();

        $fieldMap = [
            'id' => 'id',
            'code' => 'code',
            'name' => 'name',
            'price' => 'price',
            'min' => 'min_stock',
        ];

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;
            
            $dbField = $fieldMap[$field] ?? $field;
            $query->where($dbField, 'LIKE', "%$value%");
        }

        foreach ($sorts as $field => $dir) {
            $dbField = $fieldMap[$field] ?? $field;
            $query->orderBy($dbField, $dir);
        }

        if (empty($sorts)) {
            $query->orderBy('id', 'desc');
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        $data = collect($paginator->items())->map(fn($r) => [
            'id' => $r->id,
            'name' => $r->name,
            'code' => (string)($r->code ?? ''),
            'price' => (float)($r->price ?? 0),
            'min' => (int)($r->min_stock ?? 0),
        ]);

        return [
            'data' => $data,
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
