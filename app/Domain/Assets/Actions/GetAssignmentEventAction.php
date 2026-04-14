<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Modals\AssignmentModalData;
use App\Domain\Assets\Models\AssetEvent;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetAssignmentEventAction
{
    use AsAction;

    public function handle(int $eventId): AssignmentModalData
    {
        $event = AssetEvent::query()->findOrFail($eventId);

        return new AssignmentModalData(
            employee_id: $event->employee_id,
            hardware:    $event->hardware ?? [],
            software:    $event->software ?? [],
            notes:       $event->notes,
        );
    }
}
