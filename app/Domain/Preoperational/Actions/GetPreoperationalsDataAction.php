<?php

declare(strict_types=1);

namespace App\Domain\Preoperational\Actions;

use App\Domain\Preoperational\Data\TableData;
use App\Domain\Preoperational\Models\Preoperational;
use App\Domain\Preoperational\Queries\PreoperationalsTableQuery;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetPreoperationalsDataAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, string>  $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): PaginatedResult
    {
        $paginator = PreoperationalsTableQuery::make()
            ->apply($filters, $sorts)
            ->paginate($page, $size);

        /** @var list<TableData> $items */
        $items = array_values(
            $paginator->getCollection()->map(static fn (Preoperational $preop, int $key): TableData => TableData::fromModel($preop))->all()
        );

        return new PaginatedResult(
            items: array_values($items),
            lastPage: $paginator->lastPage(),
            total: $paginator->total(),
        );
    }
}
