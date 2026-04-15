<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Actions;

use App\Domain\Ppe\Data\PpeItemTableData;
use App\Domain\Ppe\Queries\PpeItemTableQuery;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetPpeItemDataAction
{
    use AsAction;

    /**
     * @param array<string,mixed>  $filters
     * @param array<string,string> $sorts
     * @return PaginatedResult<PpeItemTableData>
     */
    public function handle(array $filters, array $sorts, int $page, int $size): PaginatedResult
    {
        $paginator = PpeItemTableQuery::make()->apply($filters, $sorts)->paginate($page, $size);

        return new PaginatedResult(
            items: array_map(
                static fn ($row) => PpeItemTableData::fromModel($row),
                $paginator->items()
            ),
            total: $paginator->total(),
            lastPage: $paginator->lastPage(),
        );
    }
}
