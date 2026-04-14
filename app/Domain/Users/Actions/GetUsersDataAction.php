<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Users\Data\TableData;
use App\Domain\Users\Models\User;
use App\Domain\Users\Queries\UserTableQuery;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetUsersDataAction
{
    use AsAction;

    /**
     * Filters and sorts follow raw Tabulator format:
     *   $filters = [['field' => 'name', 'value' => 'foo'], ...]
     *   $sorts   = [['field' => 'name', 'dir' => 'asc'], ...]
     *
     * @param  array<int, array{field: string, value: mixed}>  $filters
     * @param  array<int, array{field: string, dir: string}>  $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): PaginatedResult
    {
        $paginator = UserTableQuery::make()
            ->apply($filters, $sorts)
            ->paginate($page, $size);

        /** @var LengthAwarePaginator<int, User> $paginator */
        $items = array_values(
            $paginator->through(static fn (mixed $user): TableData => TableData::fromModel($user))
                ->getCollection()
                ->all()
        );

        return new PaginatedResult(
            items: $items,
            lastPage: $paginator->lastPage(),
            total: $paginator->total(),
        );
    }
}
