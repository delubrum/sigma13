<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Actions;

use App\Domain\Maintenance\Data\TableData;
use App\Domain\Maintenance\Queries\MaintenanceTableQuery;
use App\Domain\Shared\Data\PaginatedResult;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetMaintenanceDataAction
{
    use AsAction;

    /**
     * @param array<string, mixed> $filters
     * @param array<string, string> $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): PaginatedResult
    {
        $user = Auth::user();
        $userId = Auth::id() ?? 93;
        /** @var array<string> $permissions */
        $permissions = $user ? (array) json_decode($user->permissions ?? '[]', true) : [];

        $query = MaintenanceTableQuery::build($filters, $userId, $permissions);

        // TODO: apply sorts if needed in MaintenanceTableQuery or here
        
        $paginator = $query->paginate(perPage: $size, page: $page);

        $items = $paginator->getCollection()->map(fn($model) => TableData::fromModel($model))->values()->all();

        return new PaginatedResult(
            items: $items,
            lastPage: $paginator->lastPage(),
            total: $paginator->total()
        );
    }
}
