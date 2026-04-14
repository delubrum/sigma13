<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Tabs\MaintenancesTableData;
use App\Domain\Shared\Data\PaginatedResult;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetAssetMaintenancesAction
{
    use AsAction;

    /** @return PaginatedResult<MaintenancesTableData> */
    public function handle(int $assetId, int $page = 1, int $size = 25): PaginatedResult
    {
        $paginator = DB::table('mnt')
            ->leftJoin('users', 'mnt.user_id', '=', 'users.id')
            ->select('mnt.*', 'users.name as user_name')
            ->where('mnt.asset_id', $assetId)
            ->whereNotNull('mnt.ended_at')
            ->latest('mnt.ended_at')
            ->paginate($size, ['*'], 'page', $page);

        /** @var list<MaintenancesTableData> $items */
        $items = array_values(
            $paginator->getCollection()
                ->map(fn (mixed $row, int|string $key): MaintenancesTableData => MaintenancesTableData::fromModel($row))
                ->all()
        );

        return new PaginatedResult(
            items: array_values($items),
            lastPage: $paginator->lastPage(),
            total: $paginator->total(),
        );
    }
}
