<?php

declare(strict_types=1);

namespace App\Domain\MaintenanceP\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        public readonly int $id,
        #[MapInputName('scheduled_start')]
        public readonly ?string $start,
        #[MapInputName('scheduled_end')]
        public readonly ?string $end,
        #[MapInputName('asset_hostname')]
        public readonly ?string $asset,
        public readonly string $status,
        #[MapInputName('days_diff_count')]
        public readonly string $days,
        public readonly ?string $activity,
        #[MapInputName('frequency_value')]
        public readonly ?string $frequency,
        public readonly ?string $started,
        public readonly ?string $attended,
        #[MapInputName('closed_at')]
        public readonly ?string $closed,
    ) {}

    /** @return list<Column> */
    public static function columns(): array
    {
        return [
            Column::make(title: 'Inicio', field: 'start', width: 110),
            Column::make(title: 'Vence', field: 'end', width: 110),
            Column::make(title: 'Activo', field: 'asset', width: 150, headerFilter: 'input'),
            Column::make(title: 'Actividad', field: 'activity', width: 200),
            Column::make(title: 'Frecuencia', field: 'frequency', width: 100),
            Column::make(title: 'Días', field: 'days', width: 70, hozAlign: 'center', formatter: 'html'),
            Column::make(title: 'Estado', field: 'status', width: 100),
        ];
    }
}
