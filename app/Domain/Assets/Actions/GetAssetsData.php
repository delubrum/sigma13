<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Table;
use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Queries\AssetTableQuery;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetAssetsData
{
    use AsAction;

    /**
     * @param array<string, mixed> $filters
     * @param array<string, string> $sorts
     * @return array{data: array<int, Table>, last_page: int, total: int}
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $paginator = AssetTableQuery::make()
            ->apply($filters, $sorts)
            ->paginate($page, $size);

        /** @var \Illuminate\Support\Collection<int, Table> $items */
        $items = $paginator->through(static fn(Asset $asset) => Table::fromModel($asset))->getCollection();

        return [
            'data'      => array_values($items->all()),
            'last_page' => $paginator->lastPage(),
            'total'     => $paginator->total(),
        ];
    }
}