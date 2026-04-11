<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetUsersTableData
{
    use AsAction;

    /**
     * Core logic to fetch and filter users for Tabulator.
     * 
     * @param array{sort?: array<int, array{field: string, dir: string}>, filter?: array<int, array{field: string, value: mixed}>, size?: int} $options
     */
    public function handle(array $options = []): LengthAwarePaginator
    {
        $query = User::query();

        // Ordenamiento
        $sorts = $options['sort'] ?? [];
        foreach ($sorts as $s) {
            $field = $s['field'] === 'isActive' ? 'is_active' : ($s['field'] === 'createdAt' ? 'created_at' : $s['field']);
            $query->orderBy($field, $s['dir']);
        }

        if (empty($sorts)) {
            $query->latest();
        }

        // Filtros básicos
        $filters = $options['filter'] ?? [];
        foreach ($filters as $f) {
            $field = $f['field'] ?? null;
            $value = $f['value'] ?? null;
            if ($field && $value !== null) {
                $dbField = $field === 'isActive' ? 'is_active' : $field;
                if ($dbField === 'is_active') {
                    $query->where($dbField, $value);
                } else {
                    $query->where($dbField, 'ilike', "%{$value}%");
                }
            }
        }

        return $query->paginate($options['size'] ?? 15);
    }
}
