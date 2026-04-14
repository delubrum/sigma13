<?php

declare(strict_types=1);

namespace App\Domain\Operations\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class EvaluationTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly int $id,

        #[Column(title: 'Date', field: 'date', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $date,

        #[Column(title: 'User', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $user,

        #[Column(title: 'Nit', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $nit,

        #[Column(title: 'Supplier', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $supplier,

        #[Column(title: 'Type', headerHozAlign: 'center', headerFilter: 'list')]
        public readonly string $type,

        #[Column(title: 'Result', headerHozAlign: 'center', headerFilter: 'list')]
        public readonly string $result,
    ) {}
}
