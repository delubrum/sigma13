<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class PreoperationalTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly int $id,

        #[Column(title: 'Date', field: 'date', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $date,

        #[Column(title: 'Vehicle', headerHozAlign: 'center', headerFilter: 'list')]
        public readonly string $vehicle,

        #[Column(title: 'User', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $user,
    ) {}
}
