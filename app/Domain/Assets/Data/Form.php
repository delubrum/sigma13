<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class Form extends Data
{
    public function __construct(
        #[Required, Max(100)]
        public string $area,

        #[Required, Max(100)]
        public string $hostname,

        #[Required, Max(100)]
        public string $serial,

        #[Required, Max(100)]
        public string $brand,

        #[Required, Max(100)]
        public string $model,

        #[Required, Max(100)]
        public string $kind,

        #[Required, Max(50)]
        public string $status,

        #[Max(100)]
        public ?string $cpu = null,

        #[Max(50)]
        public ?string $ram = null,

        #[Max(50)]
        public ?string $ssd = null,

        #[Max(50)]
        public ?string $hdd = null,

        #[Max(100)]
        public ?string $so = null,

        #[Max(100)]
        public ?string $sap = null,

        public ?float $price = null,
        public ?Carbon $acquisition_date = null,

        #[Max(100)]
        public ?string $invoice = null,

        #[Max(150)]
        public ?string $supplier = null,

        #[Max(100)]
        public ?string $warranty = null,

        #[Max(100)]
        public ?string $classification = null,

        public ?int $confidentiality = null,
        public ?int $integrity = null,
        public ?int $availability = null,

        #[Max(255)]
        public ?string $location = null,

        #[Max(50)]
        public ?string $phone = null,

        #[Max(100)]
        public ?string $work_mode = null,

        #[Max(255)]
        public ?string $url = null,

        #[Max(150)]
        public ?string $operator = null,
    ) {}
}
