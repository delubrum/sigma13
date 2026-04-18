<?php

declare(strict_types=1);

namespace App\Domain\Stock\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class UpsertData extends Data
{
    public function __construct(
        public readonly ?int $id,

        #[Field(
            label: 'Descripción (Nombre)',
            type: 'text',
            placeholder: 'Ej: Casco de Seguridad tipo II',
            width: FieldWidth::Full,
        )]
        #[Required, Max(255)]
        public readonly string $name,

        #[Field(
            label: 'Tipo',
            type: 'select',
            options: [
                'EPP'      => 'EPP',
                'Dotación' => 'Dotación',
                'Insumo'   => 'Insumo',
            ],
            widget: 'slimselect',
            width: FieldWidth::Half,
        )]
        #[Required]
        public readonly string $kind,

        #[Field(
            label: 'SAP ID (Código)',
            type: 'number',
            placeholder: 'Código interno o SAP',
            width: FieldWidth::Half,
        )]
        public readonly ?int $code,

        #[Field(
            label: 'Área Responsable',
            type: 'number',
            placeholder: 'Area',
            width: FieldWidth::Half,
        )]
        
        #[Required]
        public readonly ?string $area,

        #[Field(
            label: 'Stock Mínimo',
            type: 'number',
            width: FieldWidth::Half,
        )]
        #[Required, Numeric]
        public readonly ?int $min_stock = 0,

        #[Field(
            label: 'Stock Inicial (Histórico)',
            type: 'number',
            step: '0.01',
            width: FieldWidth::Half,
        )]
        public readonly float $initial_stock = 0,
    ) {}
}
