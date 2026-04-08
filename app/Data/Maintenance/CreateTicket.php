<?php

declare(strict_types=1);

namespace App\Data\Maintenance;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class CreateTicket extends Data
{
    public function __construct(
        #[Required, Max(120)]
        public readonly string $facility,

        #[Required]
        public readonly string $priority,

        #[Required, Max(2000)]
        public readonly string $description,
    ) {}
}
