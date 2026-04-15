<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class PpeEntryTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID',      width: 70,  headerFilter: 'input')]
        public readonly string $id,

        #[Column(title: 'Nombre',  width: 250, headerFilter: 'input')]
        public readonly string $name,

        #[Column(title: 'Stock',   width: 100, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $stock,
    ) {}

    public static function fromRow(mixed $row): self
    {
        /** @var object $row */
        return new self(
            id:    (string) ($row->item_id ?? ''),
            name:  (string) ($row->name ?? ''),
            stock: (string) ($row->stock ?? '0'),
        );
    }
}
