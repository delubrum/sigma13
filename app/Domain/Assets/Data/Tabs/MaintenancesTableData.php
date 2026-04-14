<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Tabs;

use App\Domain\Shared\Data\Column;
use Illuminate\Support\Facades\Date;
use Spatie\LaravelData\Data;

final class MaintenancesTableData extends Data
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

    /**
     * @param  mixed  $row  stdClass (from DB::table with user_name JOIN) or any object with matching properties
     */
    public static function fromModel(mixed $row): self
    {
        /** @var object $row */
        $date = isset($row->created_at)
            ? Date::parse((string) $row->created_at)->format('d/m/Y H:i')
            : '---';

        $closed = isset($row->ended_at)
            ? Date::parse((string) $row->ended_at)->format('d/m/Y H:i')
            : '---';

        return new self(
            id: (int) ($row->id ?? 0),
            type: (string) ($row->subtype ?? 'Corrective'),
            date: $date,
            user: (string) ($row->user_name ?? '---'),
            description: (string) ($row->description ?? '---'),
            closed: $closed,
            status: (string) ($row->status ?? '---'),
            rating: (int) ($row->rating ?? 0),
        );
    }
}
