<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class DeliveryTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 80, hozAlign: 'center', headerFilter: 'input')]
        public readonly int $id,

        #[Column(title: 'Fecha', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $date,

        #[Column(title: 'Nombre', width: 200, headerFilter: 'input')]
        public readonly string $name,

        #[Column(title: 'Tipo', width: 120, headerFilter: 'input')]
        public readonly string $type,

        #[Column(title: 'Empleado', width: 200, headerFilter: 'input')]
        public readonly string $employee,

        #[Column(title: 'Área', width: 150, headerFilter: 'input')]
        public readonly ?string $area,

        #[Column(title: 'Usuario', width: 150, headerFilter: 'input')]
        public readonly string $user,

        #[Column(title: 'Notas', width: 250, headerFilter: 'input')]
        public readonly ?string $notes,
    ) {}
}
