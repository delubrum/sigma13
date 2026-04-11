<?php

declare(strict_types=1);

namespace App\Domain\ItMaintenance\Data;

use App\Domain\ItMaintenance\Models\It;
use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 60, hozAlign: 'center', headerFilter: 'input')]
        public readonly int $id,

        #[Column(title: 'Fecha', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $date,

        #[Column(title: 'Usuario', width: 150, headerFilter: 'input')]
        public readonly string $user,

        #[Column(title: 'Sede', width: 120, headerFilter: 'input')]
        public readonly ?string $facility,

        #[Column(title: 'Activo', width: 200, headerFilter: 'input')]
        public readonly ?string $asset,

        #[Column(title: 'Prioridad', width: 100, headerFilter: 'input')]
        public readonly ?string $priority,

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
                    'Closed' => 'Closed', 
                    'Rated' => 'Rated', 
                    'Rejected' => 'Rejected'
                ], 
                'clearable' => true
            ]
        )]
        public readonly string $status,

        #[Column(title: 'Descripción', width: 250, formatter: 'textarea', headerFilter: 'input')]
        public readonly ?string $description,

        #[Column(title: 'Asignado', width: 150, headerFilter: 'input')]
        public readonly ?string $assignee,

        #[Column(title: 'Días', width: 80, hozAlign: 'center', headerFilter: 'input')]
        public readonly int $days,

        #[Column(title: 'Iniciado', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly ?string $started_at,

        #[Column(title: 'Atendido', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly ?string $attended,

        #[Column(title: 'Cerrado', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly ?string $closed,

        #[Column(title: 'Horas', width: 80, hozAlign: 'center', headerFilter: 'input')]
        public readonly float $time,

        #[Column(title: 'SGC', width: 100, headerFilter: 'input')]
        public readonly ?string $sgc,

        #[Column(title: 'Rating', width: 80, hozAlign: 'center', headerFilter: 'number')]
        public readonly ?int $rating,
    ) {}
}
