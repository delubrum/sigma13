<?php

declare(strict_types=1);

namespace App\Domain\Cbm\Data;

use Spatie\LaravelData\Data;

final class SidebarData extends Data
{
    /**
     * @param array<int, \App\Domain\Shared\Data\SidebarItem> $properties
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $subtitle,
        public string $color,
        public array $properties,
        public mixed $model = null,
    ) {}
}
