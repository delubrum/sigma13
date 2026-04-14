<?php

declare(strict_types=1);

namespace App\Domain\Quality\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class DocumentTableData extends Data
{
    public function __construct(
        #[Column(title: 'Category', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $category,

        #[Column(title: 'Type', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $type,

        #[Column(title: 'Name', headerHozAlign: 'center', headerFilter: 'input', formatter: 'html')]
        public readonly string $name,

        #[Column(title: 'Date', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $date,

        #[Column(title: 'Size', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $size,
    ) {}
}
