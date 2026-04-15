<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class PpeDeliveryTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID',       width: 70,  headerFilter: 'input')]
        public readonly string $id,

        #[Column(title: 'Fecha',    width: 110, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $date,

        #[Column(title: 'EPP',      width: 200, headerFilter: 'input')]
        public readonly string $name,

        #[Column(title: 'Tipo',     width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $type,

        #[Column(title: 'Empleado', width: 180, headerFilter: 'input')]
        public readonly string $employee,

        #[Column(title: 'Área',     width: 140, headerFilter: 'input')]
        public readonly string $area,

        #[Column(title: 'Usuario',  width: 130, headerFilter: 'input')]
        public readonly string $user,

        #[Column(title: 'Notas',    width: 200, formatter: 'textarea', headerFilter: 'input')]
        public readonly string $notes,
    ) {}

    public static function fromModel(mixed $row): self
    {
        /** @var object $row */
        return new self(
            id:       (string) ($row->id ?? ''),
            date:     isset($row->created_at) ? substr((string) $row->created_at, 0, 10) : '',
            name:     (string) ($row->name ?? ''),
            type:     (string) ($row->kind ?? ''),
            employee: (string) ($row->employee_name ?? ''),
            area:     (string) ($row->area_name ?? ''),
            user:     (string) ($row->user_name ?? ''),
            notes:    (string) ($row->notes ?? ''),
        );
    }
}
