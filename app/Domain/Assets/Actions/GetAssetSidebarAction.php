<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\SidebarData;
use App\Domain\Assets\Models\Asset;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetAssetSidebarAction
{
    use AsAction;

    public function handle(int $id): SidebarData
    {
        $asset = Asset::query()->with(['currentAssignment.employee'])->findOrFail($id);

        return SidebarData::fromModel($asset);
    }
}
