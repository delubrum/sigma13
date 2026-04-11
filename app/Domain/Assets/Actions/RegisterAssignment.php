<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Modals\Assignment as AssignmentData;
use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Models\AssetEvent;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

final class RegisterAssignment
{
    use AsAction;

    /**
     * Core logic to register a new asset assignment.
     * Infra-agnostic. Returns the created event.
     */
    public function handle(int $assetId, AssignmentData $data): AssetEvent
    {
        $event = AssetEvent::create([
            'kind' => 'assignment',
            'asset_id' => $assetId,
            'employee_id' => $data->employee_id,
            'hardware' => $data->hardware,
            'software' => $data->software,
            'notes' => $data->notes,
            'user_id' => Auth::id(),
        ]);

        Asset::where('id', $assetId)->update(['status' => 'assigned']);

        return $event;
    }
}
