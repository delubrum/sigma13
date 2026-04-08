<?php

declare(strict_types=1);

namespace App\Data\Shared;

use Spatie\LaravelData\Data;

final class Config extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly string $subtitle,
        public readonly string $icon,
        public readonly string $newButtonLabel = 'Nuevo',
        public readonly bool $showKpi = false,
        public readonly string $modalWidth = '50%',
        /** @var list<array{title: string, field: string, width?: int, hozAlign?: string, headerHozAlign?: string, formatter?: string, headerFilter?: string, headerFilterParams?: array<string, mixed>, headerFilterPlaceholder?: string, clearable?: bool}> $columns */
        public readonly array $columns = [],
        /** @var list<Field> */
        public readonly array $formFields = [],
        /** @var list<Tabs> */
        public readonly array $tabs = [],
        public readonly bool $multipart = false,
    ) {}
}
