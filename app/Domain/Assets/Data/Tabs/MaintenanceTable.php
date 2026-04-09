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
        public readonly string $type,
        public readonly string $date,
        public readonly string $user,
        public readonly string $description,
        public readonly string $closed,
        public readonly string $status,
        public readonly int $rating,
    ) {}

    /** @return list<Column> */
    public static function columns(): array
    {
        return [
            Column::make(title: 'Tipo', field: 'type', width: 120),
            Column::make(title: 'Fecha', field: 'date', width: 130),
            Column::make(title: 'Solicitante', field: 'user', width: 200),
            Column::make(title: 'Descripción', field: 'description'),
            Column::make(title: 'Cerrado', field: 'closed', width: 130),
            Column::make(title: 'Estado', field: 'status', width: 100),
            Column::make(title: 'Calif.', field: 'rating', width: 80),
        ];
    }

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
