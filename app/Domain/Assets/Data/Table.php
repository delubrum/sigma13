<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data;

use App\Domain\Assets\Models\Asset;
use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        #[Column(title: 'ID', width: 60, hozAlign: 'center')]
        public readonly int $id,

        #[Column(title: 'Área', width: 120)]
        public readonly ?string $area,

        #[Column(title: 'SAP', width: 100)]
        public readonly ?string $sap,

        #[Column(title: 'Serial', width: 120)]
        public readonly ?string $serial,

        #[Column(title: 'Responsable', width: 200)]
        public readonly ?string $assignee,

        #[Column(title: 'Hostname', width: 150)]
        public readonly ?string $hostname,

        #[Column(title: 'Marca', width: 100)]
        public readonly ?string $brand,

        #[Column(title: 'Modelo', width: 150)]
        public readonly ?string $model,

        #[Column(title: 'Tipo', width: 100)]
        public readonly ?string $kind,

        #[Column(title: 'CPU', width: 120)]
        public readonly ?string $cpu,

        #[Column(title: 'RAM', width: 80, hozAlign: 'center')]
        public readonly ?string $ram,

        #[Column(title: 'SSD', width: 80, hozAlign: 'center')]
        public readonly ?string $ssd,

        #[Column(title: 'HDD', width: 80, hozAlign: 'center')]
        public readonly ?string $hdd,

        #[Column(title: 'S.O.', width: 120)]
        public readonly ?string $so,

        #[Column(title: 'Precio', width: 100, hozAlign: 'right')]
        public readonly ?string $price,

        #[Column(title: 'Fecha', width: 120, hozAlign: 'center')]
        public readonly ?string $date,

        #[Column(title: 'Factura', width: 120)]
        public readonly ?string $invoice,

        #[Column(title: 'Proveedor', width: 150)]
        public readonly ?string $supplier,

        #[Column(title: 'Garantía', width: 120)]
        public readonly ?string $warranty,

        #[Column(title: 'Modo Trabajo', width: 120, hozAlign: 'center')]
        public readonly ?string $work_mode,

        #[Column(title: 'Ubicación', width: 150)]
        public readonly ?string $location,

        #[Column(title: 'Teléfono', width: 120)]
        public readonly ?string $phone,

        #[Column(title: 'Operador', width: 120)]
        public readonly ?string $operator,

        #[Column(
            title: 'Estado', 
            width: 120, 
            hozAlign: 'center', 
            formatter: 'html', 
            headerFilter: 'list', 
            headerFilterParams: [
                'values' => [
                    'available' => 'Disponible', 
                    'assigned' => 'Asignado', 
                    'maintenance' => 'Mantenimiento', 
                    'retired' => 'Retirado'
                ], 
                'clearable' => true
            ]
        )]
        public readonly ?string $status,

        #[Column(title: 'Clasificación', width: 120)]
        public readonly ?string $classification,

        #[Column(title: 'Conf.', width: 60, hozAlign: 'center')]
        public readonly ?int $confidentiality,

        #[Column(title: 'Int.', width: 60, hozAlign: 'center')]
        public readonly ?int $integrity,

        #[Column(title: 'Disp.', width: 60, hozAlign: 'center')]
        public readonly ?int $availability,

        #[Column(title: 'Criticidad', width: 100, hozAlign: 'center', formatter: 'html')]
        public readonly ?string $criticality,
    ) {}

    public static function fromModel(Asset $asset): self
    {
        return new self(
            id: $asset->id,
            area: $asset->area ?? '',
            sap: $asset->sap,
            serial: $asset->serial,
            assignee: $asset->assignee_name,
            hostname: $asset->hostname,
            brand: $asset->brand ?? '',
            model: $asset->model ?? '',
            kind: $asset->kind ?? '',
            cpu: $asset->cpu ?? '',
            ram: $asset->ram ?? '',
            ssd: $asset->ssd ?? '',
            hdd: $asset->hdd ?? '',
            so: $asset->so ?? '',
            price: (string) ($asset->price ?? 0),
            date: $asset->acquisition_date ? $asset->acquisition_date->format('Y-m-d') : '',
            invoice: $asset->invoice ?? '',
            supplier: $asset->supplier ?? '',
            warranty: $asset->warranty ?? '',
            work_mode: $asset->work_mode,
            location: $asset->location ?? '',
            phone: $asset->phone ?? '',
            operator: $asset->operator ?? '',
            status: $asset->status_label ?? '',
            classification: $asset->classification ?? '',
            confidentiality: $asset->confidentiality,
            integrity: $asset->integrity,
            availability: $asset->availability,
            criticality: $asset->criticality ?? '',
        );
    }
}
