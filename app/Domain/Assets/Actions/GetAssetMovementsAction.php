<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Tabs\MovementsTableData;
use App\Domain\Assets\Models\AssetEvent;
use App\Domain\Shared\Data\PaginatedResult;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetAssetMovementsAction
{
    use AsAction;

    /** @return PaginatedResult<MovementsTableData> */
    public function handle(int $assetId, int $page = 1, int $size = 25): PaginatedResult
    {
        $latestAssignmentId = AssetEvent::query()
            ->where('asset_id', $assetId)
            ->where('kind', 'assignment')
            ->orderByDesc('id')
            ->value('id');

        $paginator = AssetEvent::query()
            ->where('asset_id', $assetId)
            ->with('employee')
            ->orderByDesc('id')
            ->paginate($size, ['*'], 'page', $page);

        /** @var list<MovementsTableData> $items */
        $items = array_values(
            $paginator->getCollection()
                ->map(fn (AssetEvent $event, int|string $key): MovementsTableData => MovementsTableData::fromModel(
                    $event,
                    $event->id === $latestAssignmentId,
                ))
                ->all()
        );

        return new PaginatedResult(
            items: array_values($items),
            lastPage: $paginator->lastPage(),
            total: $paginator->total(),
        );
    }
}
