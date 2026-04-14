<?php

declare(strict_types=1);

namespace App\Domain\Evaluation\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class EvaluationsTableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 60, hozAlign: 'center', hide: true)]
        public readonly int $id,

        #[Column(title: 'Fecha', width: 150, hozAlign: 'center')]
        public readonly string $date,

        #[Column(title: 'Usuario', width: 150)]
        public readonly string $user,

        #[Column(title: 'Nit', width: 120, hozAlign: 'center')]
        public readonly string $nit,

        #[Column(title: 'Proveedor')]
        public readonly string $supplier,

        #[Column(title: 'Tipo', width: 120, hozAlign: 'center')]
        public readonly string $type,

        #[Column(
            title: 'Resultado', 
            width: 120, 
            hozAlign: 'center', 
            formatter: 'progress', 
            formatterParams: ['min' => 0, 'max' => 100, 'legend' => true, 'color' => ['#ef4444', '#f59e0b', '#10b981']]
        )]
        public readonly float $result,
    ) {}
}
