<?php

declare(strict_types=1);

namespace App\Domain\Performance\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class PerformanceTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', headerHozAlign: 'center', headerFilter: 'input', width: 70)]
        public readonly int $id,

        #[Column(title: 'Period', field: 'created_at', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $created_at,

        #[Column(title: 'Employee', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $employee,

        #[Column(title: 'Status', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $status,

        #[Column(title: 'Self', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly ?float $self,

        #[Column(title: 'Leader', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly ?float $leader,

        #[Column(title: 'Peers', field: 'peer', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly ?float $peer,

        #[Column(title: 'Score', headerHozAlign: 'center', headerFilter: 'input', formatter: 'color')]
        public readonly float $score,
    ) {}
}
