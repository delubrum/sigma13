<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Data\Table;
use App\Domain\Users\Models\User;
use App\Domain\Users\Queries\UserTableQuery;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetUsersData
{
    use AsAction;

    /**
     * @param array<string, mixed> $filters
     * @param array<string, string> $sorts
     * @return array{data: array<int, Table>, last_page: int, total: int}
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $paginator = UserTableQuery::make()
            ->apply($filters, $sorts)
            ->paginate($page, $size);

        $paginator->through(static fn(User $user) => Table::fromModel($user));

        return [
            'data'      => array_values($paginator->items()),
            'last_page' => $paginator->lastPage(),
            'total'     => $paginator->total(),
        ];
    }
}