<?php

declare(strict_types=1);

namespace App\Domain\IT\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        public readonly int $id,
        #[MapInputName('created_at')]
        public readonly string $date,
        #[MapInputName('requestor_name')]
        public readonly ?string $requestor,
        public readonly ?string $facility,
        #[MapInputName('asset_hostname')]
        public readonly ?string $asset,
        public readonly ?string $priority,
        public readonly string $description,
        #[MapInputName('assignee_name')]
        public readonly ?string $assignee,
        #[MapInputName('days_count')]
        public readonly int $days,
        #[MapInputName('started_at')]
        public readonly ?string $startedAt,
        #[MapInputName('ended_at')]
        public readonly ?string $endedAt,
        #[MapInputName('closed_at')]
        public readonly ?string $closedAt,
        #[MapInputName('time_sum')]
        public readonly float $time,
        public readonly string $status,
        public readonly ?string $sgc,
        public readonly ?int $rating,
    ) {}

    /** @return list<Column> */
    public static function columns(): array
    {
        return [
            Column::make(title: 'Fecha', field: 'date', width: 140),
            Column::make(title: 'Solicitante', field: 'requestor', width: 200, headerFilter: 'input'),
            Column::make(title: 'Sede', field: 'facility', width: 100),
            Column::make(title: 'Activo', field: 'asset', width: 150, headerFilter: 'input'),
            Column::make(title: 'Prioridad', field: 'priority', width: 100),
            Column::make(title: 'Descripción', field: 'description', width: 300, headerFilter: 'input'),
            Column::make(title: 'Asignado', field: 'assignee', width: 150),
            Column::make(title: 'Días', field: 'days', width: 70),
            Column::make(title: 'Estado', field: 'status', width: 100),
        ];
    }
}
