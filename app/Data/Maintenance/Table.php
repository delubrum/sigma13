<?php

declare(strict_types=1);

namespace App\Data\Maintenance;

use App\Models\Mnt;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $date,
        public readonly ?string $requestor,
        public readonly ?string $facility,
        public readonly ?string $asset,
        public readonly ?string $priority,
        public readonly string $description,
        public readonly ?string $assignee,
        public readonly int $days,
        public readonly ?string $startedAt,
        public readonly ?string $attendedAt,
        public readonly ?string $closedAt,
        public readonly float $time,
        public readonly string $status,
        public readonly ?string $sgc,
        public readonly ?string $cause,
        public readonly ?int $rating,
    ) {}

    public static function fromModel(Mnt $ticket): self
    {
        return new self(
            id: $ticket->id,
            date: $ticket->created_at?->format('Y-m-d H:i') ?? '—',
            requestor: (string) ($ticket->getAttribute('requestor_name') ?? '—'),
            facility: $ticket->facility,
            asset: mb_convert_case((string) ($ticket->getAttribute('asset_hostname') ?? '—'), MB_CASE_TITLE, 'UTF-8'),
            priority: $ticket->priority,
            description: $ticket->description ?? '',
            assignee: (string) ($ticket->getAttribute('assignee_name') ?? '—'),
            days: (int) ($ticket->getAttribute('days_count') ?? 0),
            startedAt: $ticket->started_at?->format('Y-m-d H:i'),
            attendedAt: $ticket->ended_at?->format('Y-m-d H:i'),
            closedAt: $ticket->closed_at?->format('Y-m-d H:i'),
            time: (float) ($ticket->getAttribute('time_sum') ?? 0),
            status: $ticket->status ?? 'Open',
            sgc: $ticket->sgc,
            cause: $ticket->root_cause,
            rating: $ticket->rating,
        );
    }
}
