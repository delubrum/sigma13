<?php

declare(strict_types=1);

namespace App\Domain\Operations\Data;

use App\Domain\Shared\Attributes\Column;
use Spatie\LaravelData\Data;

final class DocumentTableData extends Data
{
    public function __construct(
        #[Column(title: 'Category', field: 'category')]
        public string $category,

        #[Column(title: 'Type', field: 'type')]
        public string $type,

        #[Column(title: 'Name', field: 'name')]
        public string $name,

        #[Column(title: 'Date', field: 'date')]
        public string $date,

        #[Column(title: 'Size', field: 'size')]
        public string $size,
    ) {}
}
