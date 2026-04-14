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
     * @return array{asset: PublicShowResourceData, correctives: Collection<int, object>, preventives: Collection<int, object>}
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
            ->orderByDesc('ended_at')
            ->limit(3)
            ->get();

        $preventives = DB::table('mnt_preventive_form')
            ->where('asset_id', $asset->id)
            ->whereNotNull('last_performed_at')
            ->orderByDesc('last_performed_at')
            ->limit(3)
            ->get();

        return [
            'asset'       => PublicShowResourceData::fromModel($asset),
            'correctives' => $correctives,
            'preventives' => $preventives,
        ];
    }
}
