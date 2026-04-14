<?php

declare(strict_types=1);

namespace App\Domain\Shared\Data;

use Spatie\LaravelData\Data;

final class Config extends Data
{
    public function __construct(
        public readonly string $title,
        public readonly string $icon,
        public readonly ?string $subtitle = null,
        public readonly string $newButtonLabel = 'Nuevo',
        public readonly bool $showKpi = false,
        public readonly ?string $modalWidth = null,
        /** @var list<Column> $columns */
        public readonly array $columns = [],
        /** @var list<Field> */
        public readonly array $formFields = [],
        /** @var list<Tabs> */
        public readonly array $tabs = [],
        /** @var list<ActionOption> */
        public readonly array $options = [],
        public readonly bool $multipart = false,
    ) {}
}
