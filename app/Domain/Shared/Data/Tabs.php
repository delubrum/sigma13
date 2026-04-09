<?php

declare(strict_types=1);

namespace App\Domain\Shared\Data;

use Spatie\LaravelData\Data;

final class Tabs extends Data
{
    public function __construct(
        public readonly string $key,      // 'details', 'assignments'
        public readonly string $label,    // 'Details', 'Assignments'
        public readonly string $icon,     // 'ri-information-line'
        public readonly string $route,    // route name: 'assets.tabs.details'
        public readonly bool $default = false,
    ) {}
}
