<?php

declare(strict_types=1);

namespace App\Domain\JobProfiles\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 70, headerFilter: 'input')]
        public readonly string $id,

        #[Column(title: 'Código', width: 110, headerFilter: 'input')]
        public readonly string $code,

        #[Column(title: 'Actualizado', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $created_at,

        #[Column(title: 'Nombre', width: 200, headerFilter: 'input')]
        public readonly string $name,

        #[Column(title: 'División', width: 160, headerFilter: 'input')]
        public readonly string $division,

        #[Column(title: 'Reporta a', width: 180, headerFilter: 'input')]
        public readonly string $reports_to,

        #[Column(title: 'Modalidad', width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $work_mode,

        #[Column(title: 'Horario', width: 130, headerFilter: 'input')]
        public readonly string $schedule,

        #[Column(title: 'Viajes', width: 80, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $travel,

        #[Column(title: 'Reloc.', width: 80, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $relocation,
    ) {}

    public static function fromModel(mixed $row): self
    {
        /** @var object $row */
        return new self(
            id: (string) ($row->id ?? ''),
            code: (string) ($row->code ?? ''),
            created_at: isset($row->created_at) ? substr((string) $row->created_at, 0, 10) : '',
            name: (string) ($row->name ?? ''),
            division: (string) ($row->division_name ?? ''),
            reports_to: (string) ($row->reports_to_name ?? ''),
            work_mode: (string) ($row->work_mode ?? ''),
            schedule: (string) ($row->schedule ?? ''),
            travel: (string) ($row->travel ?? ''),
            relocation: (string) ($row->relocation ?? ''),
        );
    }
}
