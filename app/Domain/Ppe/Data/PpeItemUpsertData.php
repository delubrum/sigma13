<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Data;

final class PpeItemUpsertData extends Data
{
    public function __construct(
        public readonly ?int $id,

        #[Field(label: 'Nombre', width: FieldWidth::Full, placeholder: 'Ej: Casco de seguridad')]
        public readonly string $name,

        #[Field(label: 'Código SAP', width: FieldWidth::Half, placeholder: 'Ej: 100-0001')]
        public readonly ?string $code,

        #[Field(label: 'Precio', width: FieldWidth::Quarter, placeholder: '0.00')]
        public readonly ?string $price,

        #[Field(label: 'Stock mínimo', width: FieldWidth::Quarter, placeholder: '0')]
        public readonly ?int $min_stock,
    ) {}
}
