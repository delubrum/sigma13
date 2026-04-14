<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Tabs\PreventiveTaskResourceData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetAssetPreventiveAction
{
    use AsAction;

    /** @return Collection<int, PreventiveTaskResourceData> */
    public function handle(int $assetId): Collection
    {
        return DB::table('mnt_preventive_form')
            ->where('asset_id', $assetId)
            ->get()
            ->map(fn (object $row): PreventiveTaskResourceData => PreventiveTaskResourceData::fromStdClass($row));
    }
}
