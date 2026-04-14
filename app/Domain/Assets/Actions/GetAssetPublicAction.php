<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\PublicShowResourceData;
use App\Domain\Assets\Models\Asset;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetAssetPublicAction
{
    use AsAction;

    /**
     * @return array{asset: PublicShowResourceData, correctives: Collection<int, \stdClass>, preventives: Collection<int, \stdClass>}
     */
    public function handle(string $serial): array
    {
        /** @var Asset $asset */
        $asset = Asset::query()
            ->with(['currentAssignment.employee', 'media'])
            ->when(
                is_numeric($serial),
                fn ($q) => $q->where('id', (int) $serial),
                fn ($q) => $q->where('serial', $serial),
            )
            ->firstOrFail();

        $correctives = DB::table('mnt')
            ->where('asset_id', $asset->id)
            ->whereNotNull('ended_at')
            ->latest('ended_at')
            ->limit(3)
            ->get();

        $preventives = DB::table('mnt_preventive_form')
            ->where('asset_id', $asset->id)
            ->whereNotNull('last_performed_at')
            ->latest('last_performed_at')
            ->limit(3)
            ->get();

        return [
            'asset' => PublicShowResourceData::fromModel($asset),
            'correctives' => $correctives,
            'preventives' => $preventives,
        ];
    }
}
