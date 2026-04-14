<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class CbmTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', headerHozAlign: 'center', headerFilter: 'input', width: 70)]
        public readonly int $id,

        #[Column(title: 'Project', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $project,

        #[Column(title: 'User', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $user,

        #[Column(title: 'Date', field: 'date', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $date,

        #[Column(title: 'Items', field: 'total_items', hozAlign: 'center')]
        public readonly int $total_items,
    ) {}
}
