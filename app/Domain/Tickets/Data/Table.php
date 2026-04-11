<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 60, hozAlign: 'center', headerFilter: 'input')]
        public readonly int $id,

        #[Column(title: 'Tipo', width: 120, headerFilter: 'input')]
        public readonly string $type,

        #[Column(title: 'Fecha', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $date,

        #[Column(title: 'Usuario', width: 150, headerFilter: 'input')]
        public readonly string $user,

        #[Column(title: 'Sede', width: 150, headerFilter: 'input')]
        public readonly ?string $facility,

        #[Column(title: 'Prioridad', width: 100, headerFilter: 'input')]
        public readonly ?string $priority,

        #[Column(title: 'Descripción', width: 250, formatter: 'textarea', headerFilter: 'input')]
        public readonly ?string $description,

        #[Column(title: 'Días', width: 80, hozAlign: 'center', headerFilter: 'number')]
        public readonly int $days,

        #[Column(title: 'Iniciado', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly ?string $started,

        #[Column(title: 'Cerrado', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly ?string $closed,

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
                    'Closed' => 'Closed', 
                    'Rejected' => 'Rejected'
                ], 
                'clearable' => true
            ]
        )]
        public readonly string $status,
    ) {}
}
