<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Tabs\DetailsResourceData;
use App\Domain\Assets\Models\Asset;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetAssetDetailsAction
{
    use AsAction;

    public function handle(int $id): DetailsResourceData
    {
        $asset = Asset::query()->findOrFail($id);

        return new DetailsResourceData(
            acquisition_date: $asset->acquisition_date?->format('Y-m-d') ?? '---',
            price: number_format((float) ($asset->price ?? 0), 0, ',', '.'),
            supplier: $asset->supplier ? strtoupper((string) $asset->supplier) : '---',
            invoice: $asset->invoice ?? '---',
            cpu: $asset->cpu ?? '---',
            ram: $asset->ram ?? '---',
            ssd: $asset->ssd ?? 'N/A',
            hdd: $asset->hdd ?? 'N/A',
            so: $asset->so ? ucwords((string) $asset->so) : '---',
        );
    }
}
