<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Modals\AssignmentModalData;
use App\Domain\Assets\Models\AssetEvent;
use Lorisleiva\Actions\Concerns\AsAction;

final class UpdateAssignmentAction
{
    use AsAction;

    public function handle(int $eventId, AssignmentModalData $data, int $userId): AssetEvent
    {
        $event = AssetEvent::query()->findOrFail($eventId);

        $event->update([
            'employee_id' => $data->employee_id,
            'hardware' => $data->hardware,
            'software' => $data->software,
            'notes' => $data->notes,
            'user_id' => $userId,
        ]);

        return $event->fresh() ?? $event;
    }
}
