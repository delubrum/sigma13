<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Data;

final class UpsertData extends Data
{
    public function __construct(
        public readonly ?int $id,

        #[Field(label: 'Shape', width: FieldWidth::Half, placeholder: 'Ej: ES-Cover-001')]
        public readonly string $geometry_shape,

        #[Field(label: 'Company', width: FieldWidth::Half, widget: 'slimselect', route: 'extrusion.options.companies')]
        public readonly ?string $company_id,

        #[Field(label: 'Category', width: FieldWidth::Full, widget: 'slimselect', route: 'extrusion.options.categories')]
        public readonly ?string $category_id,

        #[Field(label: 'B', width: FieldWidth::Quarter, placeholder: '0.000')]
        public readonly ?string $b,

        #[Field(label: 'H', width: FieldWidth::Quarter, placeholder: '0.000')]
        public readonly ?string $h,

        #[Field(label: 'E1', width: FieldWidth::Quarter, placeholder: '0.000')]
        public readonly ?string $e1,

        #[Field(label: 'E2', width: FieldWidth::Quarter, placeholder: '0.000')]
        public readonly ?string $e2,
    ) {}
}
