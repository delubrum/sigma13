<?php

declare(strict_types=1);

namespace App\Domain\Shared\Data;

use Spatie\LaravelData\Data;

final class Field extends Data
{
    /**
     * @param  array<int|string, string>  $options
     */
    public function __construct(
        public readonly string $name,
        public readonly string $label,
        public readonly string $type = 'text',
        public readonly bool $required = false,
        public readonly string $placeholder = '',
        public readonly ?string $hint = null,
        public readonly array $options = [],

        /** 'flatpickr' | 'flatpickr-range' | 'slimselect' | 'sigma-file' | 'asset-hardware' | 'asset-software' | 'asset-condition-grid' | null */
        public readonly ?string $widget = null,

        public readonly FieldWidth $width = FieldWidth::Full,
        public readonly ?string $accept = null,
    ) {}
}
