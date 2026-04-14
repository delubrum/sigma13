<?php

declare(strict_types=1);

namespace App\Domain\Operations\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class EquipmentDeliveryTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', headerHozAlign: 'center', headerFilter: 'input', width: 70)]
        public readonly int $id,

        #[Column(title: 'Fecha', field: 'date', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $date,

        #[Column(title: 'Nombre', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $name,

        #[Column(title: 'Empleado', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $employee,

        #[Column(title: 'Usuario', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $user,

        #[Column(title: 'Notas', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $notes,
    ) {}
}
