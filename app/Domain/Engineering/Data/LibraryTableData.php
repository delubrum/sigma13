<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class LibraryTableData extends Data
{
    public function __construct(
        #[Column(title: 'Img', field: 'img', width: 100, hozAlign: 'center', headerSort: false, formatter: 'image')]
        public readonly string $img,

        #[Column(title: 'Shape', field: 'geometry_shape', headerFilter: 'input')]
        public readonly string $geometry_shape,

        #[Column(title: 'Company', field: 'company', headerFilter: 'input')]
        public readonly string $company,

        #[Column(title: 'Category', field: 'category', headerFilter: 'input')]
        public readonly string $category,

        #[Column(title: 'B', field: 'b', width: 80, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $b,

        #[Column(title: 'H', field: 'h', width: 80, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $h,

        #[Column(title: 'E1', field: 'e1', width: 80, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $e1,

        #[Column(title: 'E2', field: 'e2', width: 80, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $e2,

        #[Column(title: 'Clicks', field: 'clicks', formatter: 'html', headerFilter: 'input')]
        public readonly string $clicks,

        #[Column(title: 'System', field: 'system', formatter: 'html', headerFilter: 'input')]
        public readonly string $system,

        #[Column(title: 'Files', field: 'files', formatter: 'html', headerSort: false)]
        public readonly string $files,
    ) {}
}
