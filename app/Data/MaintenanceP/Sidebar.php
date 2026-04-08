<?php

declare(strict_types=1);

namespace App\Data\MaintenanceP;

use App\Models\MntPreventive;
use Spatie\LaravelData\Data;

final class Sidebar extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $status,
        public readonly ?string $activity,
        public readonly ?string $frequency,
        public readonly ?string $asset,
        public readonly ?string $scheduledStart,
        public readonly ?string $scheduledEnd,
        public readonly ?string $started,
        public readonly ?string $attended,
        public readonly ?string $closedAt,
    ) {}

    public static function fromModel(MntPreventive $record): self
    {
        return new self(
            id: $record->id,
            status: $record->status ?? 'Open',
            activity: $record->activity,    // @phpstan-ignore-line
            frequency: $record->frequency,  // @phpstan-ignore-line
            asset: $record->asset?->hostname . ' | ' . $record->asset?->serial,
            scheduledStart: $record->scheduled_start, // @phpstan-ignore-line
            scheduledEnd: $record->scheduled_end,     // @phpstan-ignore-line
            started: $record->started,
            attended: $record->attended,
            closedAt: $record->closed_at?->format('Y-m-d H:i'),
        );
    }
}
