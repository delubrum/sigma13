<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Tabs;

use App\Domain\Maintenance\Models\Mnt;
use App\Domain\Shared\Data\Column;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

final class MaintenanceTable extends Data
{
    public function __construct(
        public readonly int $id,

        #[Column(title: 'Tipo', width: 120)]
        public readonly string $type,

        #[Column(title: 'Fecha', width: 130)]
        public readonly string $date,

        #[Column(title: 'Solicitante', width: 200)]
        public readonly string $user,

        #[Column(title: 'Descripción')]
        public readonly string $description,

        #[Column(title: 'Cerrado', width: 130)]
        public readonly string $closed,

        #[Column(title: 'Estado', width: 100)]
        public readonly string $status,

        #[Column(title: 'Calif.', width: 80)]
        public readonly int $rating,
    ) {}

    public static function fromModel(Mnt $request): self
    {
        $date = $request->created_at instanceof Carbon
            ? $request->created_at->format('d/m/Y H:i')
            : '---';

        $closed = $request->closed_at instanceof Carbon
            ? $request->closed_at->format('d/m/Y H:i')
            : '---';

        return new self(
            id: $request->id,
            type: $request->subtype ?? 'Corrective',
            date: $date,
            user: $request->user->name ?? '---',
            description: $request->description ?? '---',
            closed: $closed,
            status: $request->status ?? '---',
            rating: $request->rating ?? 0,
        );
    }
}
