<?php

declare(strict_types=1);

namespace App\Domain\Shared\Data;

use Spatie\LaravelData\Data;

final class SidebarItem extends Data
{
    public function __construct(
        public string $label,
        public ?string $value,
        public ?string $icon = null,
        public ?string $url = null,
        public ?string $linkIcon = null,
    ) {}
}
