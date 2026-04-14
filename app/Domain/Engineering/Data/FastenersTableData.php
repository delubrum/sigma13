<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class FastenersTableData extends Data
{
    public function __construct(
        #[Column(title: 'Img', field: 'img', width: 100, hozAlign: 'center', headerSort: false, formatter: 'image')]
        public readonly string $img,

        #[Column(title: 'Code', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $code,

        #[Column(title: 'Description', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $description,

        #[Column(title: 'Category', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $category,

        #[Column(title: 'Head', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $head,

        #[Column(title: 'Screwdriver', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $screwdriver,

        #[Column(title: 'Diameter', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $diameter,

        #[Column(title: 'Length', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $length,

        #[Column(title: 'Observation', headerHozAlign: 'center', headerFilter: 'input')]
        public readonly string $observation,

        #[Column(title: 'Files', field: 'files', formatter: 'html', headerSort: false)]
        public readonly string $files,
    ) {}
}
