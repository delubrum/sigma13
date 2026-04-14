<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'Código', width: 110, headerFilter: 'input')]
        public readonly string $id,

        #[Column(title: 'Fecha', width: 110, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $date,

        #[Column(title: 'Creador', width: 150, headerFilter: 'input')]
        public readonly string $creator,

        #[Column(title: 'Responsable', width: 150, headerFilter: 'input')]
        public readonly string $responsible,

        #[Column(title: 'Proceso', width: 160, headerFilter: 'input')]
        public readonly string $process,

        #[Column(
            title: 'Estado',
            width: 120,
            hozAlign: 'center',
            formatter: 'html',
            headerFilter: 'list',
            headerFilterParams: [
                'values' => [
                    'Analysis' => 'Analysis',
                    'Plan'     => 'Plan',
                    'Closure'  => 'Closure',
                    'Closed'   => 'Closed',
                    'Rejected' => 'Rejected',
                    'Canceled' => 'Canceled',
                ],
                'clearable' => true,
            ]
        )]
        public readonly string $status,

        #[Column(title: 'Tipo', width: 130, headerFilter: 'input')]
        public readonly string $type,

        #[Column(title: 'Fuente', width: 130, headerFilter: 'input')]
        public readonly string $source,

        #[Column(title: 'Descripción', width: 280, formatter: 'textarea', headerFilter: 'input')]
        public readonly string $description,
    ) {}

    public static function fromModel(mixed $row): self
    {
        /** @var object $row */
        $status = (string) ($row->status ?? 'Analysis');
        $color = match ($status) {
            'Analysis' => 'border-blue-500 text-blue-500',
            'Plan'     => 'border-yellow-500 text-yellow-500',
            'Closure'  => 'border-orange-500 text-orange-500',
            'Closed'   => 'border-green-500 text-green-500',
            default    => 'border-red-500 text-red-500',
        };

        return new self(
            id:          (string) ($row->code ?? $row->id ?? ''),
            date:        isset($row->created_at) ? (string) $row->created_at : '',
            creator:     (string) ($row->creator_name ?? ''),
            responsible: (string) ($row->responsible_name ?? ''),
            process:     (string) ($row->process ?? ''),
            status:      "<span class=\"px-2 py-0.5 rounded border {$color} font-bold uppercase text-[10px]\">{$status}</span>",
            type:        (string) ($row->type ?? ''),
            source:      (string) ($row->source ?? ''),
            description: (string) ($row->description ?? ''),
        );
    }
}
