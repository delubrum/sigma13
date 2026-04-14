<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Modals\ReturnModalData;
use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Models\AssetEvent;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class RegisterReturnAction
{
    use AsAction;

    /**
     * @param int $assetId
     * @param ReturnModalData $data
     * @param int $userId
     * @return AssetEvent
     * @throws \DomainException
     */
    public function handle(int $assetId, ReturnModalData $data, int $userId): AssetEvent
    {
        return DB::transaction(function () use ($assetId, $data, $userId) {
            /** @var Asset $asset */
            $asset = Asset::query()->lockForUpdate()->findOrFail($assetId);

            if ($asset->status !== 'assigned') {
                throw new \DomainException("El activo {$assetId} no se puede devolver porque su estado es: {$asset->status}");
            }

            $event = AssetEvent::create([
                'kind'        => 'return',
                'asset_id'    => $assetId,
                'employee_id' => $asset->currentAssignment?->employee_id,
                'hardware'    => $data->hardware,
                'wipe'        => $data->wipe,
                'notes'       => $data->notes,
                'user_id'     => $userId,
                'created_at'  => now(),
            ]);

            $asset->update(['status' => 'available']);

            return $event;
        });
    }
}