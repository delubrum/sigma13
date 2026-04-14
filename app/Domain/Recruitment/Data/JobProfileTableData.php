<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class JobProfileTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly int $id,

        #[Column(title: 'Code', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $code,

        #[Column(title: 'Updated', field: 'created_at', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $created_at,

        #[Column(title: 'Name', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $name,

        #[Column(title: 'Division', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $division,

        #[Column(title: 'Reports To', field: 'reports_to', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $reports_to,

        #[Column(title: 'Mode', field: 'work_mode', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $work_mode,

        #[Column(title: 'Schedule', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $schedule,

        #[Column(title: 'Travel', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $travel,

        #[Column(title: 'Relocation', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $relocation,
    ) {}
}
