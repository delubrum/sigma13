<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class EntryTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 80, hozAlign: 'center', headerFilter: 'input')]
        public readonly int $id,

        #[Column(title: 'Nombre', width: 300, headerFilter: 'input')]
        public readonly string $name,

        #[Column(title: 'Stock Actual', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly int $total,
    ) {}
}
