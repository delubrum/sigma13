<?php

declare(strict_types=1);

namespace App\Domain\Cbm\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 70, hozAlign: 'center', headerFilter: 'input')]
        public int $id,

        #[Column(title: 'Project', width: 250, headerFilter: 'input')]
        public string $project,

        #[Column(title: 'User', width: 150, headerFilter: 'input')]
        public string $user,

        #[Column(title: 'Date', width: 150, headerFilter: 'input')]
        public string $date,

        #[Column(title: 'Items', width: 100, hozAlign: 'center')]
        public int $total_items,
    ) {}
}
