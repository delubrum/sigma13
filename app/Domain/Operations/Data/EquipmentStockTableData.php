<?php

declare(strict_types=1);

namespace App\Domain\Operations\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class EquipmentStockTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly int $id,

        #[Column(title: 'Name', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $name,

        #[Column(title: 'Total', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly int $total,
    ) {}
}
