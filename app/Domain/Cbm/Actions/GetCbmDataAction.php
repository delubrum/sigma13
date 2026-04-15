<?php

declare(strict_types=1);

namespace App\Domain\Cbm\Actions;

use App\Domain\Cbm\Data\TableData;
use App\Domain\Cbm\Models\Cbm;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetCbmDataAction
{
    use AsAction;

    /**
     * @param array<string, mixed> $filters
     * @param array<string, string> $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): PaginatedResult
    {
        $query = Cbm::query()->with('user');

        foreach ($filters as $field => $value) {
            if ($value) {
                if ($field === 'user') {
                    $query->whereHas('user', fn($q) => $q->where('username', 'LIKE', "%{$value}%"));
                } else {
                    $query->where($field, 'LIKE', "%{$value}%");
                }
            }
        }

        foreach ($sorts as $field => $dir) {
            $query->orderBy($field, $dir);
        }

        if (empty($sorts)) {
            $query->orderBy('created_at', 'desc');
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        $items = $paginator->getCollection()->map(fn(Cbm $c) => new TableData(
            id: (int) $c->id,
            project: $c->project,
            user: $c->user->username ?? 'Unknown',
            date: $c->created_at?->format('Y-m-d') ?? '—',
            total_items: (int) $c->total_items
        ))->values()->all();

        return new PaginatedResult(
            items: $items,
            lastPage: $paginator->lastPage(),
            total: $paginator->total()
        );
    }
}
