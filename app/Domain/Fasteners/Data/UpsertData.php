<?php

declare(strict_types=1);

namespace App\Domain\Fasteners\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\MapInputName;

final class UpsertData extends Data
{
    public function __construct(
        public ?int $id,

        #[Field(label: 'Code', width: FieldWidth::Half, required: true)]
        public string $code,

        #[Field(label: 'Category', width: FieldWidth::Half)]
        public ?string $category,

        #[Field(label: 'Description', width: FieldWidth::Full)]
        public ?string $description,

        #[Field(label: 'Head Type', width: FieldWidth::Half)]
        public ?string $head,

        #[Field(label: 'Screwdriver', width: FieldWidth::Half)]
        public ?string $screwdriver,

        #[Field(label: 'Diameter', width: FieldWidth::Half)]
        public ?string $diameter,

        #[Field(label: 'Length', width: FieldWidth::Half)]
        #[MapInputName('item_length')]
        public ?string $item_length,

        #[Field(label: 'Observation', type: 'textarea', width: FieldWidth::Full)]
        public ?string $observation,

        #[Field(label: 'Files/Drawing', type: 'file', widget: 'sigma-file', width: FieldWidth::Full)]
        public ?array $files = null,
    ) {}
}
