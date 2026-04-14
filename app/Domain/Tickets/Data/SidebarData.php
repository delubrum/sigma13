<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Data;

use App\Domain\Tickets\Models\Ticket;
use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;

final class SidebarData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $status,
        public readonly string $kind,
        public readonly string $username,
        public readonly string $createdAt,
        public readonly ?string $facility,
        public readonly ?string $startedAt,
        public readonly ?string $closedAt,
        public readonly ?string $description,
        public readonly ?string $priority,
        public readonly ?int $assetId,
        public readonly ?int $assigneeId,
        public readonly ?string $sgc,
        public readonly ?string $rootCause,
        public readonly ?int $rating,
        /** @var array<int, array{name: string, url: string}> */
        public readonly array $evidences = [],
        /** @var array<int, array{id: int, label: string}> */
        public readonly array $assets = [],
        /** @var array<int, array{id: int, name: string}> */
        public readonly array $assignees = [],
    ) {}

    public static function fromModel(Ticket $ticket, Collection $assets = new Collection(), Collection $assignees = new Collection()): self
    {
        // Soporte para ambos directorios por la fusión de módulos
        $dirs = [
            "uploads/tickets/userpics/{$ticket->id}/",
            "uploads/mnt/userpics/{$ticket->id}/"
        ];
        
        $evidences = [];
        foreach ($dirs as $dir) {
            $fullPath = public_path($dir);
            if (is_dir($fullPath)) {
                $files = glob($fullPath . '*');
                if ($files) {
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            $evidences[] = [
                                'name' => 'Evidence',
                                'url'  => asset($dir . basename($file)),
                            ];
                        }
                    }
                }
            }
        }

        return new self(
            id:          $ticket->id,
            status:      $ticket->status ?? 'Open',
            kind:        $ticket->kind ?? '-',
            username:    $ticket->user?->name ?? 'Unknown',
            createdAt:   $ticket->created_at?->format('Y-m-d H:i') ?? '-',
            facility:    $ticket->facility,
            startedAt:   $ticket->started_at?->format('Y-m-d H:i'),
            closedAt:    $ticket->closed_at?->format('Y-m-d H:i'),
            description: $ticket->description,
            priority:    $ticket->priority,
            assetId:     $ticket->asset_id,
            assigneeId:  $ticket->assignee_id,
            sgc:         $ticket->sgc,
            rootCause:   $ticket->root_cause,
            rating:      (int) $ticket->rating,
            evidences:   $evidences,
            assets:      $assets->toArray(),
            assignees:   $assignees->toArray(),
        );
    }
}