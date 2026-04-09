<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Data;

use Spatie\LaravelData\Data;

final class UpdateRecruitment extends Data
{
    public function __construct(
        public readonly string $field,
        public readonly mixed $value,
    ) {}
}
