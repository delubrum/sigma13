<?php

declare(strict_types=1);

namespace App\Domain\Fasteners\Actions;

use App\Domain\Fasteners\Data\TableData;
use App\Domain\Fasteners\Models\Fastener;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetFastenersDataAction
{
    use AsAction;

    /**
     * @param array<string, mixed> $filters
     * @param array<string, string> $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): PaginatedResult
    {
        $query = Fastener::query();

        foreach ($filters as $field => $value) {
            if ($value) {
                // Map frontend field to database column if needed
                $column = $field === 'length' ? 'item_length' : $field;
                $query->where($column, 'LIKE', "%{$value}%");
            }
        }

        foreach ($sorts as $field => $dir) {
            $column = $field === 'length' ? 'item_length' : $field;
            $query->orderBy($column, $dir);
        }

        if (empty($sorts)) {
            $query->orderBy('id', 'desc');
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        $items = $paginator->getCollection()->map(fn($m) => TableData::fromModel($m))->values()->all();

        return new PaginatedResult(
            items: $items,
            lastPage: $paginator->lastPage(),
            total: $paginator->total()
        );
    }
}
