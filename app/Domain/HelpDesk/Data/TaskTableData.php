<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Data;

use App\Domain\HelpDesk\Models\IssueItem;
use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TaskTableData extends Data
{
    public function __construct(
        #[Column(title: 'Fecha', width: 130, hozAlign: 'center')]
        public readonly string $created_at,

        #[Column(title: 'Operario', width: 140)]
        public readonly string $user,

        #[Column(title: 'Complejidad', width: 110, hozAlign: 'center')]
        public readonly string $complexity,

        #[Column(title: 'Tipo', width: 110, hozAlign: 'center')]
        public readonly string $action_taken,

        #[Column(title: 'Duración (min)', width: 120, hozAlign: 'center')]
        public readonly int $duration_minutes,

        #[Column(title: 'Notas', width: 300)]
        public readonly string $notes,

        #[Column(title: 'Evidencia', width: 80, hozAlign: 'center', formatter: 'html')]
        public readonly string $evidence,
    ) {}

    public static function fromModel(mixed $item): self
    {
        /** @var IssueItem $item */
        $media = $item->getFirstMedia('evidence');
        $evidence = $media
            ? "<a href=\"{$media->getTemporaryUrl(now()->addMinutes(30))}\" target=\"_blank\" class=\"text-blue-500 hover:underline\"><i class=\"ri-file-search-line text-base\"></i></a>"
            : '—';

        return new self(
            created_at:      $item->created_at?->format('d/m/Y H:i') ?? '—',
            user:            $item->user?->name ?? '—',
            complexity:      $item->complexity ?? '—',
            action_taken:    $item->action_taken ?? '—',
            duration_minutes: (int) ($item->duration_minutes ?? 0),
            notes:           $item->notes ?? '—',
            evidence:        $evidence,
        );
    }
}
