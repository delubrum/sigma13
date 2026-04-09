<?php

declare(strict_types=1);

namespace App\Domain\MaintenanceP\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class Sidebar extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $status,
        public readonly ?string $activity,
        public readonly ?string $frequency,
        public readonly ?string $asset,
        #[MapInputName('scheduled_start')]
        public readonly ?string $scheduledStart,
        #[MapInputName('scheduled_end')]
        public readonly ?string $scheduledEnd,
        public readonly ?string $started,
        public readonly ?string $attended,
        #[MapInputName('closed_at')]
        public readonly ?string $closedAt,
    ) {}

}
