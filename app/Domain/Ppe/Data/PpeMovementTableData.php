<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class PpeMovementTableData extends Data
{
    public function __construct(
        #[Column(title: 'Fecha',    width: 150, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $date,

        #[Column(title: 'Tipo',     width: 90,  hozAlign: 'center', headerFilter: 'input')]
        public readonly string $type,

        #[Column(title: 'Qty',      width: 80,  hozAlign: 'center', headerFilter: 'input')]
        public readonly string $qty,

        #[Column(title: 'Persona',  width: 200, headerFilter: 'input')]
        public readonly string $person,
    ) {}

    public static function fromRow(mixed $row): self
    {
        /** @var object $row */
        return new self(
            date:   (string) ($row->date ?? ''),
            type:   (string) ($row->type ?? ''),
            qty:    (string) ($row->qty ?? ''),
            person: (string) ($row->person ?? ''),
        );
    }
}
