<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Actions;

use App\Domain\Ppe\Data\PpeEntryTableData;
use App\Domain\Ppe\Queries\PpeEntryTableQuery;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetPpeEntryDataAction
{
    use AsAction;

    /**
     * @param array<string,mixed>  $filters
     * @param array<string,string> $sorts
     * @return PaginatedResult<PpeEntryTableData>
     */
    public function handle(array $filters, array $sorts, int $page, int $size): PaginatedResult
    {
        $paginator = PpeEntryTableQuery::make()->paginate($filters, $sorts, $page, $size);

        return new PaginatedResult(
            items: array_map(
                static fn ($row) => PpeEntryTableData::fromRow($row),
                $paginator->items()
            ),
            total: $paginator->total(),
            lastPage: $paginator->lastPage(),
        );
    }
}
