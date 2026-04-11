<?php

declare(strict_types=1);

namespace App\Domain\Identity\Data;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class LoginData extends Data
{
    public function __construct(
        #[Required, Email]
        public string $email,

        #[Required]
        public ?string $password = null,

        public bool $remember = false,

        public ?string $recaptcha_token = null,
    ) {}
}
