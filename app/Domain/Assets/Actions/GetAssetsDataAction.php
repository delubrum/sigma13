<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\TableData;
use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Queries\AssetsTableQuery;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetAssetsDataAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, string>  $sorts
     * @return PaginatedResult<TableData>
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): PaginatedResult
    {
        $paginator = AssetsTableQuery::make()
            ->apply($filters, $sorts)
            ->paginate($page, $size);

        /** @var list<TableData> $items */
        $items = array_values(
            $paginator->getCollection()->map(static fn (Asset $asset, int $key): TableData => TableData::fromModel($asset))->all()
        );

        return new PaginatedResult(
            items: array_values($items),
            lastPage: $paginator->lastPage(),
            total: $paginator->total(),
        );
    }
}
