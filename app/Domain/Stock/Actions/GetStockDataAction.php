<?php

declare(strict_types=1);

namespace App\Domain\Stock\Actions;

use App\Domain\Stock\Data\TableData;
use App\Domain\Stock\Models\Stock;
use App\Domain\Stock\Queries\StockTableQuery;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetStockDataAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, string>  $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): PaginatedResult
    {
        $paginator = StockTableQuery::make()
            ->apply($filters, $sorts)
            ->paginate($page, $size);

        /** @var list<TableData> $items */
        $items = array_values(
            $paginator->getCollection()->map(static fn (Stock $issue): TableData => TableData::fromModel($issue))->all()
        );

        return new PaginatedResult(
            items: $items,
            lastPage: $paginator->lastPage(),
            total: $paginator->total(),
        );
    }
}
