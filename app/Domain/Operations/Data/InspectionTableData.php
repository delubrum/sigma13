<?php

declare(strict_types=1);

namespace App\Domain\Operations\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class InspectionTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', headerHozAlign: 'center', headerFilter: 'input', width: 70)]
        public readonly int $id,

        #[Column(title: 'Created', field: 'created_at', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $created_at,

        #[Column(title: 'Due', field: 'due_date', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $due_date,

        #[Column(title: 'Asset', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $asset,

        #[Column(title: 'Frequency', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $frequency,

        #[Column(title: 'Status', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $status,

        #[Column(title: 'Days', headerHozAlign: 'center', headerFilter: 'input', formatter: 'html')]
        public readonly string $days,

        #[Column(title: 'Started', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly ?string $started,

        #[Column(title: 'Closed', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly ?string $closed,
    ) {}
}
