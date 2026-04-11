<?php

declare(strict_types=1);

namespace App\Domain\Shared\Data;

use Spatie\LaravelData\Data;

final class ActionOption extends Data
{
    public function __construct(
        public readonly string $label,
        public readonly string $icon,
        public readonly string $route, // Nombre de la ruta (Laravel route name) o URL relativa
        public readonly string $target = '#modal-body',
        public readonly int $level = 1,
        public readonly string $method = 'GET',
        public readonly ?string $confirm = null,
        public readonly ?string $prompt = null,
    ) {}
}
