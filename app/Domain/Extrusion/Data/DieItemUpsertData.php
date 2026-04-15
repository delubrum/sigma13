<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Data;

final class DieItemUpsertData extends Data
{
    public function __construct(
        public readonly ?int $id,

        #[Field(label: 'Name', width: FieldWidth::Half, placeholder: 'Ej: Cover')]
        public readonly string $name,

        #[Field(label: 'Type', width: FieldWidth::Half, type: 'select', options: ['Category' => 'Category', 'System' => 'System'])]
        public readonly string $kind,
    ) {}
}
