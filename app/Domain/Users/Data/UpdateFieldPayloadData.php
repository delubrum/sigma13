<?php

declare(strict_types=1);

namespace App\Domain\Users\Data;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class UpdateFieldPayloadData extends Data
{
    public function __construct(
        #[Required, In(['name', 'email'])]
        public string $field,

        #[Required]
        public string $value,
    ) {}
}
