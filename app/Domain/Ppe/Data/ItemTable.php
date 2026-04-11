<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class ItemTable extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 80, hozAlign: 'center', headerFilter: 'input')]
        public readonly int $id,

        #[Column(title: 'SAP', width: 120, headerFilter: 'input')]
        public readonly ?string $code,

        #[Column(title: 'Nombre', width: 300, headerFilter: 'input')]
        public readonly string $name,

        #[Column(title: 'Precio', width: 120, hozAlign: 'right', headerFilter: 'input')]
        public readonly float $price,

        #[Column(title: 'Min Stock', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly int $min_stock,
    ) {}
}
