<?php

declare(strict_types=1);

namespace App\Domain\MaintenanceP\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 60, hozAlign: 'center', headerFilter: 'input')]
        public readonly int $id,

        #[Column(title: 'Programado', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly ?string $start,

        #[Column(title: 'Vencimiento', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly ?string $end,

        #[Column(title: 'Activo', width: 250, headerFilter: 'input')]
        public readonly ?string $asset,

        #[Column(
            title: 'Estado', 
            width: 120, 
            hozAlign: 'center', 
            formatter: 'html', 
            headerFilter: 'list', 
            headerFilterParams: [
                'values' => [
                    'Open' => 'Open', 
                    'Started' => 'Started', 
                    'Attended' => 'Attended', 
                    'Closed' => 'Closed'
                ], 
                'clearable' => true
            ]
        )]
        public readonly string $status,

        #[Column(title: 'Días', width: 100, hozAlign: 'center', formatter: 'html', headerFilter: 'input')]
        public readonly string $days,

        #[Column(title: 'Actividad', width: 250, headerFilter: 'input')]
        public readonly ?string $activity,

        #[Column(title: 'Frecuencia', width: 120, headerFilter: 'input')]
        public readonly ?string $frequency,

        #[Column(title: 'Inicio Real', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly ?string $started,

        #[Column(title: 'Atendido', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly ?string $attended,

        #[Column(title: 'Cerrado', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly ?string $closed,
    ) {}
}
