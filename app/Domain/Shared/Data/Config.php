<?php

declare(strict_types=1);

namespace App\Domain\Shared\Data;

use Spatie\LaravelData\Data;

final class Config extends Data
{
    /**
     * @param  list<Column>  $columns
     * @param  list<Field>  $formFields
     * @param  list<Tabs>  $tabs
     * @param  list<ActionOption>  $options
     */
    public function __construct(
        public readonly string $title,
        public readonly string $icon,
        public readonly ?string $subtitle = null,
        public readonly string $newButtonLabel = 'Nuevo',
        public readonly bool $showKpi = false,
        public readonly ?string $modalWidth = null,
        public readonly array $columns = [],
        public readonly array $formFields = [],
        public readonly array $tabs = [],
        public readonly array $options = [],
        public readonly bool $multipart = false,
    ) {}
}
