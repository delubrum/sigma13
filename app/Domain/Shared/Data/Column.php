<?php

declare(strict_types=1);

namespace App\Domain\Shared\Data;

use Attribute;
use Spatie\LaravelData\Data;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Column extends Data
{
    public function __construct(
        public ?string $title = null,
        public ?string $field = null,
        public ?int $width = null,
        public ?int $minWidth = null,
        public ?int $maxWidth = null,
        public ?string $hozAlign = null,
        public ?string $vertAlign = null,
        public ?string $headerHozAlign = null,
        public ?string $formatter = null,
        /** @var array<string, mixed>|null */
        public ?array $formatterParams = null,
        public ?string $accessor = null,
        /** @var array<string, mixed>|null */
        public ?array $accessorParams = null,
        public ?string $headerFilter = null,
        /** @var array<string, mixed>|null */
        public ?array $headerFilterParams = null,
        public ?string $headerFilterPlaceholder = null,
        public ?string $headerFilterFunc = null,
        public bool $headerSort = true,
        public bool $headerSortTristate = false,
        public bool $resizable = true,
        public bool|string $frozen = false,
        public ?int $responsive = null,
        public ?string $tooltip = null,
        public ?string $topCalc = null,
        public ?string $bottomCalc = null,
        public ?string $sorter = null,
        /** @var array<string, mixed>|null */
        public ?array $sorterParams = null,
        public bool $clearable = false,
        public bool $hide = false,
        public ?bool $visible = null,
    ) {}

    /** @param array<string, mixed>|null $params */
    public static function make(
        ?string $title = null,
        ?string $field = null,
        ?int $width = null,
        ?string $hozAlign = null,
        ?string $formatter = null,
        ?array $params = null,
    ): self {
        return new self(
            title: $title,
            field: $field,
            width: $width,
            hozAlign: $hozAlign,
            formatter: $formatter,
            formatterParams: $params
        );
    }
}
