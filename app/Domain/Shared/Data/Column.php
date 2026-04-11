<?php

declare(strict_types=1);

namespace App\Domain\Shared\Data;

use Spatie\LaravelData\Data;

final class Column extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly string $field,
        public readonly ?int $width = null,
        public readonly ?string $hozAlign = null,
        public readonly ?string $headerHozAlign = null,
        public readonly ?string $formatter = null,
        public readonly ?string $headerFilter = null,
        /** @var array<string, mixed>|null */
        public readonly ?array $headerFilterParams = null,
        public readonly ?string $headerFilterPlaceholder = null,
        public readonly bool $clearable = false,
        public readonly bool $headerSort = true,
        public readonly ?int $responsive = null,
    ) {}

    /** @param array<string, mixed>|null $headerFilterParams */
    public static function make(
        string $title,
        string $field,
        ?int $width = null,
        ?string $hozAlign = null,
        ?string $headerHozAlign = null,
        ?string $formatter = null,
        ?string $headerFilter = null,
        ?array $headerFilterParams = null,
        ?string $headerFilterPlaceholder = null,
        bool $clearable = false,
        bool $headerSort = true,
        ?int $responsive = null,
    ): self {
        return new self(
            $title,
            $field,
            $width,
            $hozAlign,
            $headerHozAlign,
            $formatter,
            $headerFilter,
            $headerFilterParams,
            $headerFilterPlaceholder,
            $clearable,
            $headerSort,
            $responsive
        );
    }
}
