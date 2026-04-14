<?php

declare(strict_types=1);

namespace App\Domain\Operations\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class EquipmentTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly int $id,

        #[Column(title: 'Name', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $name,

        #[Column(title: 'SAP', field: 'code', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $code,

        #[Column(title: 'Price', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly float $price,

        #[Column(title: 'Min', field: 'min', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly int $min,
    ) {}
}
