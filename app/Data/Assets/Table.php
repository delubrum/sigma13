<?php

declare(strict_types=1);

namespace App\Data\Assets;

use App\Models\Asset;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $area,
        public readonly ?string $sap,
        public readonly ?string $serial,
        public readonly ?string $assignee,
        public readonly ?string $hostname,
        public readonly ?string $brand,
        public readonly ?string $model,
        public readonly ?string $kind,
        public readonly ?string $cpu,
        public readonly ?string $ram,
        public readonly ?string $ssd,
        public readonly ?string $hdd,
        public readonly ?string $so,
        public readonly ?string $invoice,
        public readonly ?string $supplier,
        public readonly ?string $warranty,
        public readonly ?string $work_mode,
        public readonly ?string $location,
        public readonly ?string $phone,
        public readonly ?string $operator,
        public readonly ?string $classification,
        public readonly ?int $confidentiality,
        public readonly ?int $integrity,
        public readonly ?int $availability,
        public readonly string $criticality,
        public readonly string $price,
        public readonly string $date,
        public readonly string $status,
    ) {}

    public static function fromModel(Asset $asset): self
    {
        $score = ($asset->confidentiality ?? 0) + ($asset->integrity ?? 0) + ($asset->availability ?? 0);

        $color = match (true) {
            $score >= 8 => 'border-red-500 text-red-500',
            $score >= 5 => 'border-orange-500 text-orange-500',
            default => 'border-sigma-b text-sigma-tx2',
        };

        return new self(
            id: $asset->id,
            area: $asset->area ?? '',
            sap: $asset->sap ?? '—',
            serial: $asset->serial,
            assignee: $asset->assignee_name ?? '—',
            hostname: $asset->hostname,
            brand: $asset->brand,
            model: $asset->model,
            kind: $asset->kind,
            cpu: $asset->cpu,
            ram: $asset->ram,
            ssd: $asset->ssd,
            hdd: $asset->hdd,
            so: $asset->so,
            invoice: $asset->invoice,
            supplier: $asset->supplier,
            warranty: $asset->warranty,
            work_mode: $asset->work_mode,
            location: $asset->location,
            phone: $asset->phone,
            operator: $asset->operator,
            classification: $asset->classification,
            confidentiality: $asset->confidentiality,
            integrity: $asset->integrity,
            availability: $asset->availability,
            criticality: sprintf('<span class="px-2 py-0.5 rounded border %s font-bold text-[10px]">%d</span>', $color, $score),
            price: '$'.number_format((float) $asset->price, 2),
            date: $asset->acquisition_date instanceof Carbon
                ? $asset->acquisition_date->format('d/m/Y')
                : '—',
            status: sprintf(
                '<span class="px-2 py-0.5 rounded border %s font-bold uppercase text-[10px]">%s</span>',
                match ($asset->status) {
                    'available' => 'border-green-500 text-green-500',
                    'assigned' => 'border-blue-500 text-blue-500',
                    'maintenance' => 'border-yellow-500 text-yellow-500',
                    'retired' => 'border-red-500 text-red-500',
                    default => 'border-sigma-b text-sigma-tx2',
                },
                $asset->status ?? 'available'
            ),
        );
    }
}
