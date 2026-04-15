<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Data;

use Spatie\LaravelData\Data;

final class SidebarData extends Data
{
    /**
     * @param  array<int, \App\Domain\Shared\Data\SidebarItem>  $properties
     * @param  array<int, mixed>  $fields
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $subtitle,
        public string $color,
        public array $properties,
        // The model can be passed if needed for dynamic forms,
        // but typically the sidebar uses properties array. We just accept it loosely.
        public mixed $model = null,
        public array $fields = [],
    ) {}
}
