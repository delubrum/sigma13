<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Modals\AssignmentModalData;
use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Models\AssetEvent;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

final class RegisterAssignmentAction
{
    use AsAction;

    public function handle(int $assetId, AssignmentModalData $data, int $userId): AssetEvent
    {
        return DB::transaction(function () use ($assetId, $data, $userId) {
            /** @var Asset $asset */
            $asset = Asset::query()->lockForUpdate()->findOrFail($assetId);

            if ($asset->status !== 'available') {
                throw new \DomainException("Activo {$assetId} no disponible (Status: {$asset->status})");
            }

            $event = AssetEvent::create([
                'kind' => 'assignment',
                'asset_id' => $assetId,
                'employee_id' => $data->employee_id,
                'hardware' => $data->hardware,
                'software' => $data->software,
                'notes' => $data->notes,
                'user_id' => $userId,
                'created_at' => now(),
            ]);

            $asset->update(['status' => 'assigned']);

            return $event;
        });
    }
}
