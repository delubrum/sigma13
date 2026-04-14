<?php

declare(strict_types=1);

namespace App\Domain\Evaluation\Data;

use Spatie\LaravelData\Data;

final class SupplierEvaluationData extends Data
{
    public function __construct(
        public int $id,
        public string $date,
        public string $user,
        public string $nit,
        public string $supplier,
        public string $type,
        public ?float $result = null,
    ) {}
}
