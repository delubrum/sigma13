<?php

declare(strict_types=1);

namespace App\Data\Assets\Tabs;

use App\Models\Mnt;
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
