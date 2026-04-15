<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Actions;

use App\Domain\Ppe\Data\PpeDeliveryTableData;
use App\Domain\Ppe\Queries\PpeDeliveryTableQuery;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetPpeDeliveryDataAction
{
    use AsAction;

    /**
     * @param array<string,mixed>  $filters
     * @param array<string,string> $sorts
     * @return PaginatedResult<PpeDeliveryTableData>
     */
    public function handle(array $filters, array $sorts, int $page, int $size): PaginatedResult
    {
        $paginator = PpeDeliveryTableQuery::make()->apply($filters, $sorts)->paginate($page, $size);

        return new PaginatedResult(
            items: array_map(
                static fn ($row) => PpeDeliveryTableData::fromModel($row),
                $paginator->items()
            ),
            total: $paginator->total(),
            lastPage: $paginator->lastPage(),
        );
    }
}
