<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Actions;

use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Tickets\Data\TableData;
use App\Domain\Tickets\Queries\TicketTableQuery;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetTicketsDataAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, string>  $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): PaginatedResult
    {
        $paginator = TicketTableQuery::make()
            ->apply($filters, $sorts)
            ->paginate($page, $size);

        /** @var list<TableData> $items */
        $items = array_values(
            $paginator->getCollection()
                ->map(fn (mixed $row, int|string $key): TableData => TableData::fromModel($row))
                ->all()
        );

        return new PaginatedResult(
            items: array_values($items),
            lastPage: $paginator->lastPage(),
            total: $paginator->total(),
        );
    }
}
