<?php

declare(strict_types=1);

namespace App\Domain\Docs\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class DocsTableData extends Data
{
    public function __construct(
        #[Column(title: 'Category', width: 150)]
        public readonly string $category,

        #[Column(title: 'Type', width: 150)]
        public readonly string $type,

        #[Column(title: 'Name', hozAlign: 'left', formatter: 'link', formatterParams: ['urlField' => 'url', 'target' => '_blank'])]
        public readonly string $name,

        #[Column(title: 'Date', width: 180, hozAlign: 'center')]
        public readonly string $date,

        #[Column(title: 'Size', width: 100, hozAlign: 'center')]
        public readonly string $size,

        public readonly string $url,
        public readonly string $raw_name,
        public readonly int $raw_size,
        public readonly int $raw_date,
    ) {}
}
