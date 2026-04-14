<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Tabs\AutomationsTableData;
use App\Domain\Shared\Data\PaginatedResult;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetAssetAutomationsAction
{
    use AsAction;

    /** @return PaginatedResult<AutomationsTableData> */
    public function handle(int $assetId, int $page = 1, int $size = 25): PaginatedResult
    {
        $paginator = DB::table('mnt_preventive_form')
            ->where('asset_id', $assetId)
            ->orderByDesc('id')
            ->paginate($size, ['*'], 'page', $page);

        /** @var list<AutomationsTableData> $items */
        $items = array_values(
            $paginator->getCollection()
                ->map(fn (object $row): AutomationsTableData => AutomationsTableData::fromModel($row))
                ->all()
        );

        return new PaginatedResult(
            items:    $items,
            lastPage: $paginator->lastPage(),
            total:    $paginator->total(),
        );
    }
}
