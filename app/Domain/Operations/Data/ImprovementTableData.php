<?php

declare(strict_types=1);

namespace App\Domain\Operations\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class ImprovementTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 60, hozAlign: 'left', headerFilter: 'input')]
        public readonly int $id,

        #[Column(title: 'Date', field: 'occurrence_date', hozAlign: 'center', headerFilter: 'input')]
        public readonly string $occurrence_date,

        #[Column(title: 'Code', headerHozAlign: 'left', headerFilter: 'input')]
        public readonly string $code,

        #[Column(title: 'Creator', headerHozAlign: 'left', headerFilter: 'input')]
        public readonly string $creator,

        #[Column(title: 'Responsible', headerHozAlign: 'left', headerFilter: 'input')]
        public readonly string $responsible,

        #[Column(title: 'Process', headerHozAlign: 'left', headerFilter: 'input')]
        public readonly string $process,

        #[Column(title: 'Status', hozAlign: 'center', headerFilter: 'list', headerFilterParams: [
            'values' => ['Open' => 'Open', 'Plan' => 'Plan', 'Analysis' => 'Analysis', 'Closure' => 'Closure', 'Closed' => 'Closed'],
            'clearable' => true
        ])]
        public readonly string $status,

        #[Column(title: 'Description', formatter: 'textarea', headerHozAlign: 'left', headerFilter: 'input')]
        public readonly string $description,

        #[Column(title: 'Type', field: 'kind', headerHozAlign: 'left', headerFilter: 'input')]
        public readonly string $kind,

        #[Column(title: 'Source', headerHozAlign: 'left', headerFilter: 'input')]
        public readonly string $source,

        #[Column(title: 'Perspective', headerHozAlign: 'left', headerFilter: 'input')]
        public readonly string $perspective,
    ) {}
}
