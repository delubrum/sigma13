<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Actions;

use App\Domain\Improvement\Data\TableData;
use App\Domain\Improvement\Queries\ImprovementTableQuery;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetImprovementDataAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, string>  $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(array $filters, array $sorts, int $page, int $size): PaginatedResult
    {
        $paginator = ImprovementTableQuery::make()
            ->apply($filters, $sorts)
            ->paginate($page, $size);

        $items = $paginator->map(fn (mixed $row): TableData => TableData::fromModel($row))->values()->all();

        return new PaginatedResult(
            items: $items,
            lastPage: $paginator->lastPage(),
            total: $paginator->total(),
        );
    }
}
