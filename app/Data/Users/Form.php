<?php

declare(strict_types=1);

namespace App\Data\Users;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class Form extends Data
{
    public function __construct(
        #[Required, Max(255)]
        public ?string $name = null,

        #[Required, Email, Max(255)]
        public ?string $email = null,

        #[Max(50)]
        public ?string $document = null,

        /** @var array<int, string> */
        public array $permissions = [],
    ) {}
}
