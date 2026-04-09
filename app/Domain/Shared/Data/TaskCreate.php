<?php

declare(strict_types=1);

namespace App\Domain\Shared\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class TaskCreate extends Data
{
    public function __construct(
        #[Required]
        public readonly string $complexity,

        #[Required]
        public readonly string $attends,

        #[Required, Min(1)]
        public readonly int $duration,

        #[Required, Max(5000)]
        public readonly string $notes,
    ) {}
}
