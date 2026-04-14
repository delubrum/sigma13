<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Data;

use App\Domain\Shared\Attributes\Column;
use Spatie\LaravelData\Data;

final class FastenerTableData extends Data
{
    public function __construct(
        #[Column(title: 'Img', field: 'img', width: 80)]
        public string $img,

        #[Column(title: 'Code', field: 'code')]
        public string $code,

        #[Column(title: 'Description', field: 'description')]
        public string $description,

        #[Column(title: 'Category', field: 'category')]
        public string $category,

        #[Column(title: 'Head', field: 'head')]
        public string $head,

        #[Column(title: 'Screwdriver', field: 'screwdriver')]
        public string $screwdriver,

        #[Column(title: 'Diameter', field: 'diameter')]
        public string $diameter,

        #[Column(title: 'Length', field: 'length')]
        public string $length,

        #[Column(title: 'Observation', field: 'observation')]
        public string $observation,

        #[Column(title: 'Files', field: 'files')]
        public string $files,
    ) {}
}
