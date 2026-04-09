<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class Form extends Data
{
    public function __construct(
        #[Required, Email]
        public readonly string $email,
    ) {}
}
