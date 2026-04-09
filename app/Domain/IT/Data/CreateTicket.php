<?php

declare(strict_types=1);

namespace App\Domain\IT\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class CreateTicket extends Data
{
    public function __construct(
        #[Required, Max(120)]
        public readonly string $facility,

        #[Required, Max(120)]
        public readonly string $kind,

        #[Required]
        public readonly string $priority,

        public readonly ?int $asset_id,

        #[Required, Max(5000)]
        public readonly string $description,
    ) {}
}
