<?php

declare(strict_types=1);

namespace App\Domain\Docs\Data;

use Spatie\LaravelData\Data;

final class DocData extends Data
{
    public function __construct(
        public string $category,
        public string $type,
        public string $name,
        public string $date,
        public string $size,
        public string $url,
        public string $raw_name,
        public int $raw_size,
        public int $raw_date,
    ) {}
}
