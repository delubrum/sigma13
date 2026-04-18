<?php

declare(strict_types=1);

namespace App\Domain\Shared\Data;

use Attribute;
use Spatie\LaravelData\Data;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Action extends Data
{
    public function __construct(
        public string $label = '',
        public string $icon = '',
        public string $route = '',
        public string $target = '#modal-body',
        public int $level = 1,
        public string $method = 'GET',
        public ?string $confirm = null,
        public ?string $prompt = null,
        public ?string $ability = null,
        public array $showWhenCan = [],  // SidebarData bool flags that must all be true; empty = always visible
    ) {}
}
