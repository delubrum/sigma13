<?php

declare(strict_types=1);

namespace App\Domain\Shared\Data;

use Attribute;
use Spatie\LaravelData\Data;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Tab extends Data
{
    public function __construct(
        public string $label = '',
        public string $icon = '',
        public string $route = '',
        public bool $default = false,
    ) {}
}
