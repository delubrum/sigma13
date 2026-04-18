<?php

declare(strict_types=1);

namespace App\Domain\Stock\Data;

use App\Domain\Shared\Data\Column;
use App\Domain\Stock\Models\Stock;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: '#', width: 65, hozAlign: 'center', frozen: true)]
        public readonly int $id,

        #[Column(title: 'Nombre', width: 300, headerFilter: 'input')]
        public readonly string $name,

        #[Column(
            title: 'Tipo',
            width: 130,
            headerFilter: 'list',
            headerFilterParams: [
                'values' => [
                    'EPP'      => 'EPP',
                    'Dotación' => 'Dotación',
                    'Insumo'   => 'Insumo',
                ],
                'clearable' => true,
            ]
        )]
        public readonly string $kind,

        #[Column(title: 'Código', width: 130, headerFilter: 'input')]
        public readonly ?int $code,

        #[Column(title: 'Stock Mín.', width: 110, hozAlign: 'center')]
        public readonly int $min_stock,

        #[Column(title: 'Stock Inicial', width: 120, hozAlign: 'right')]
        public readonly float $initial_stock,

        #[Column(title: 'Área', width: 150, headerFilter: 'input')]
        public readonly ?string $area,

        #[Column(title: 'Fecha', width: 130, hozAlign: 'center')]
        public readonly string $created_at,
    ) {}

    public static function fromModel(Stock $stock): self
    {
        return new self(
            id:         $stock->id,
            name:       $stock->name,
            kind:       $stock->kind,
            code:       $stock->code,
            min_stock:  $stock->min_stock ?? 0,
            initial_stock: (float) ($stock->initial_stock ?? 0),
            area:       $stock->area,
            created_at: $stock->created_at->format('d/m/Y H:i'),
        );
    }
}
