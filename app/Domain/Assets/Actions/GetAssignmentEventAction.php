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

        /** @var array<int, string> $hardware */
        $hardware = is_array($event->hardware) ? $event->hardware : [];
        /** @var array<int, string> $software */
        $software = is_array($event->software) ? $event->software : [];

        return new AssignmentModalData(
            employee_id: (int) ($event->employee_id ?? 0),
            hardware: $hardware,
            software: $software,
            notes: $event->notes,
        );
    }
}
