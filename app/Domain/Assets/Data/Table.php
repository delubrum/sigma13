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
        public readonly int $id,
        public readonly ?string $area,
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
        public readonly ?string $price,
        public readonly ?string $date,
        public readonly ?string $invoice,
        public readonly ?string $supplier,
        public readonly ?string $warranty,
        public readonly ?string $work_mode,
        public readonly ?string $location,
        public readonly ?string $phone,
        public readonly ?string $operator,
        public readonly ?string $status,
        public readonly ?string $classification,
        public readonly ?int $confidentiality,
        public readonly ?int $integrity,
        public readonly ?int $availability,
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

    /** @return list<Column> */
    public static function columns(): array
    {
        return [
            Column::make(title: 'ID', field: 'id', width: 60, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Área', field: 'area', width: 120, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'SAP', field: 'sap', width: 100, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Serial', field: 'serial', width: 120, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Responsable', field: 'assignee', width: 200, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Hostname', field: 'hostname', width: 150, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Marca', field: 'brand', width: 100, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Modelo', field: 'model', width: 150, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Tipo', field: 'kind', width: 100, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'CPU', field: 'cpu', width: 120, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'RAM', field: 'ram', width: 80, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'SSD', field: 'ssd', width: 80, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'HDD', field: 'hdd', width: 80, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'S.O.', field: 'so', width: 120, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Precio', field: 'price', width: 100, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Fecha', field: 'date', width: 120, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Factura', field: 'invoice', width: 120, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Proveedor', field: 'supplier', width: 150, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Garantía', field: 'warranty', width: 120, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Modo Trabajo', field: 'work_mode', width: 120, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Ubicación', field: 'location', width: 150, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Teléfono', field: 'phone', width: 120, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Operador', field: 'operator', width: 120, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Estado', field: 'status', width: 120, formatter: 'html', headerHozAlign: 'center', hozAlign: 'center', headerFilter: 'list', headerFilterParams: ['values' => ['available' => 'Disponible', 'assigned' => 'Asignado', 'maintenance' => 'Mantenimiento', 'retired' => 'Retirado'], 'clearable' => true], headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Clasificación', field: 'classification', width: 120, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Conf.', field: 'confidentiality', width: 60, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Int.', field: 'integrity', width: 60, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Disp.', field: 'availability', width: 60, headerHozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Criticidad', field: 'criticality', width: 100, formatter: 'html', headerHozAlign: 'center', hozAlign: 'center', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
        ];
    }
}
