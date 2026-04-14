<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Tabs;

use Spatie\LaravelData\Data;

final class DetailsResourceData extends Data
{
    public function __construct(
        public string $acquisition_date,
        public string $price,
        public string $supplier,
        public string $invoice,
        public string $cpu,
        public string $ram,
        public string $ssd,
        public string $hdd,
        public string $so,
    ) {}
}
