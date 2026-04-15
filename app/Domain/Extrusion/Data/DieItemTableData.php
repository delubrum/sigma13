<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class DieItemTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID',   width: 70,  hozAlign: 'center', headerFilter: 'input')]
        public readonly string $id,

        #[Column(title: 'Type', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $kind,

        #[Column(title: 'Name', width: 250, headerFilter: 'input')]
        public readonly string $name,
    ) {}

    public static function fromModel(mixed $row): self
    {
        /** @var object $row */
        return new self(
            id:   (string) ($row->id ?? ''),
            kind: (string) ($row->kind ?? ''),
            name: (string) ($row->name ?? ''),
        );
    }
}
