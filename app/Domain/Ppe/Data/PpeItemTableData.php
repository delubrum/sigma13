<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class PpeItemTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID',    width: 70,  headerFilter: 'input')]
        public readonly string $id,

        #[Column(title: 'Nombre', width: 220, headerFilter: 'input')]
        public readonly string $name,

        #[Column(title: 'SAP',   width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $code,

        #[Column(title: 'Precio', width: 100, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $price,

        #[Column(title: 'Stock mín.', width: 100, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $min_stock,
    ) {}

    public static function fromModel(mixed $row): self
    {
        /** @var object $row */
        return new self(
            id:        (string) ($row->id ?? ''),
            name:      (string) ($row->name ?? ''),
            code:      (string) ($row->code ?? ''),
            price:     (string) ($row->price ?? ''),
            min_stock: (string) ($row->min_stock ?? ''),
        );
    }
}
