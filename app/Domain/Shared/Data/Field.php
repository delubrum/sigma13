<?php

declare(strict_types=1);

namespace App\Domain\Shared\Data;

use Attribute;
use Spatie\LaravelData\Data;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Field extends Data
{
    /**
     * @param  array<int|string, string>  $options
     */
    public function __construct(
        public ?string $name = null,
        public ?string $label = null,
        public string $type = 'text',
        public bool $required = false,
        public string $placeholder = '',
        public ?string $hint = null,
        public array $options = [],

        /** 'flatpickr' | 'flatpickr-range' | 'slimselect' | 'sigma-file' | 'asset-hardware' | 'asset-software' | 'asset-condition-grid' | null */
        public ?string $widget = null,

        public FieldWidth $width = FieldWidth::Full,
        public ?string $accept = null,
        public bool $hide = false,
        public bool $disabled = false,
        public bool $readonly = false,
        public bool $multiple = false,
        public ?int $rows = null,
        public ?string $max = null,
        public ?string $min = null,
        public ?string $step = null,
        public ?string $pattern = null,
        public ?string $autocomplete = null,
    ) {}
}
