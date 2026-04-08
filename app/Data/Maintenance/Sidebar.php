<?php

declare(strict_types=1);

namespace App\Data\Maintenance;

use App\Models\Mnt;
use Spatie\LaravelData\Data;

final class Sidebar extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $status,
        public readonly ?string $priority,
        public readonly ?string $facility,
        public readonly ?string $description,
        public readonly ?string $assignee,
        public readonly ?string $user,
        public readonly ?string $sgc,
        public readonly ?string $rootCause,
        public readonly ?int $rating,
        public readonly ?string $createdAt,
        public readonly ?string $startedAt,
        public readonly ?string $closedAt,
    ) {}

    public static function fromModel(Mnt $ticket): self
    {
        return new self(
            id: $ticket->id,
            status: $ticket->status ?? 'Open',
            priority: $ticket->priority,
            facility: $ticket->facility,
            description: $ticket->description,
            assignee: $ticket->assignee?->name,
            user: $ticket->user?->name,
            sgc: $ticket->sgc,
            rootCause: $ticket->root_cause,
            rating: $ticket->rating,
            createdAt: $ticket->created_at?->format('Y-m-d H:i'),
            startedAt: $ticket->started_at?->format('Y-m-d H:i'),
            closedAt: $ticket->closed_at?->format('Y-m-d H:i'),
        );
    }
}
