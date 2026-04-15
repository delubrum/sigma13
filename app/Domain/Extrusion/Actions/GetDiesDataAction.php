<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Actions;

use App\Domain\Extrusion\Data\TableData;
use App\Domain\Extrusion\Queries\DieTableQuery;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetDiesDataAction
{
    use AsAction;

    /**
     * @param array<string,mixed>  $filters
     * @param array<string,string> $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(array $filters, array $sorts, int $page, int $size): PaginatedResult
    {
        $paginator = DieTableQuery::make()->apply($filters, $sorts)->paginate($page, $size);

        return new PaginatedResult(
            items: array_map(
                static fn ($row) => TableData::fromModel($row),
                $paginator->items()
            ),
            total: $paginator->total(),
            lastPage: $paginator->lastPage(),
        );
    }
}
