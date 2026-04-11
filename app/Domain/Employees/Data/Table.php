<?php

declare(strict_types=1);

namespace App\Domain\Employees\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 120, headerFilter: 'input')]
        public readonly string $id,

        #[Column(title: 'Nombre', width: 250, headerFilter: 'input')]
        public readonly string $name,

        #[Column(title: 'División', width: 150, headerFilter: 'input')]
        public readonly ?string $division,

        #[Column(title: 'Perfil', width: 200, headerFilter: 'input')]
        public readonly ?string $profile,

        #[Column(title: 'Ciudad', width: 120, headerFilter: 'input')]
        public readonly ?string $city,

        #[Column(title: 'Ingreso', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $start_date,

        #[Column(
            title: 'Estado', 
            width: 100, 
            hozAlign: 'center', 
            formatter: 'html', 
            headerFilter: 'list', 
            headerFilterParams: [
                'values' => [
                    '1' => 'Activo', 
                    '0' => 'Inactivo'
                ], 
                'clearable' => true
            ]
        )]
        public readonly string $status,

        #[Column(title: 'Última Actualización', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly ?string $updated_at,
    ) {}
}
