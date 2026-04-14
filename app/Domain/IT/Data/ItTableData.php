<?php

declare(strict_types=1);

namespace App\Domain\IT\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class ItTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', headerHozAlign: 'center', headerFilter: 'input', width: 70)]
        public readonly int $id,

        #[Column(title: 'Date', field: 'date', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $date,

        #[Column(title: 'User', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $user,

        #[Column(title: 'Facility', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $facility,

        #[Column(title: 'Asset', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $asset,

        #[Column(title: 'Priority', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $priority,

        #[Column(title: 'Status', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $status,

        #[Column(title: 'Assignee', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $assignee,

        #[Column(title: 'Days', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly int $days,

        #[Column(title: 'Hours', field: 'time', headerHozAlign: 'right', headerFilter: 'input')]
        public readonly float $time,

        #[Column(title: 'Rating', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly ?int $rating,
    ) {}
}
