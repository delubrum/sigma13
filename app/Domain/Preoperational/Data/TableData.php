<?php

declare(strict_types=1);

namespace App\Domain\Preoperational\Data;

use App\Domain\Assets\Models\Asset;
use App\Domain\Preoperational\Models\Preoperational;
use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 80, hozAlign: 'center', headerFilter: 'input')]
        public readonly int $id,

        #[Column(title: 'Fecha', width: 150, hozAlign: 'center', headerFilter: 'customDateRangeFilter')]
        public readonly string $date,

        #[Column(title: 'Vehículo', width: 300, headerFilter: 'input')]
        public readonly string $vehicle,

        #[Column(title: 'Usuario', width: 200, headerFilter: 'input')]
        public readonly string $user,

        #[Column(
            title: 'Estado',
            width: 120,
            hozAlign: 'center',
            formatter: 'html',
            headerFilter: 'list',
            headerFilterParams: [
                'values' => [
                    'completed' => 'Completado',
                    'draft' => 'Borrador',
                ],
                'clearable' => true,
            ]
        )]
        public readonly string $status,
    ) {}

    public static function fromModel(mixed $preop): self
    {
        /** @var Preoperational $preop */
        return new self(
            id: (int) $preop->id,
            date: $preop->created_at->format('Y-m-d H:i'),
            vehicle: self::formatVehicleName($preop->vehicle),
            user: $preop->user->username ?? '—',
            status: self::renderStatusBadge($preop->status),
        );
    }

    private static function formatVehicleName(?Asset $vehicle): string
    {
        if (! $vehicle instanceof Asset) {
            return '—';
        }

        return collect([$vehicle->hostname, $vehicle->serial, $vehicle->sap])
            ->filter()
            ->join(' || ');
    }

    private static function renderStatusBadge(?string $status): string
    {
        $color = match ($status) {
            'completed' => 'border-green-500 text-green-500',
            'draft' => 'border-yellow-500 text-yellow-500',
            default => 'border-sigma-b text-sigma-tx2',
        };

        $label = match ($status) {
            'completed' => 'Completado',
            'draft' => 'Borrador',
            default => $status ?? '—',
        };

        return "<span class=\"px-2 py-0.5 rounded border {$color} font-bold uppercase text-[10px]\">{$label}</span>";
    }
}
