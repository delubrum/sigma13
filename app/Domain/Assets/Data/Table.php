<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $area,
        public readonly ?string $sap,
        public readonly ?string $serial,
        #[MapInputName('assignee_name')]
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
        #[MapInputName('work_mode')]
        public readonly ?string $workMode,
        public readonly ?string $location,
        public readonly ?string $phone,
        public readonly ?string $operator,
        public readonly ?string $classification,
        public readonly ?int $confidentiality,
        public readonly ?int $integrity,
        public readonly ?int $availability,
        public readonly string $criticality,
        public readonly string $price,
        #[MapInputName('acquisition_date')]
        public readonly string $date,
        #[MapInputName('status_label')]
        public readonly string $status,
    ) {}

    /** @return list<Column> */
    public static function columns(): array
    {
        return [
            Column::make(title: 'SAP', field: 'sap', width: 100, headerFilter: 'input'),
            Column::make(title: 'S/N', field: 'serial', width: 120, headerFilter: 'input'),
            Column::make(title: 'Responsable', field: 'assignee', width: 200, headerFilter: 'input'),
            Column::make(title: 'Hostname', field: 'hostname', width: 150, headerFilter: 'input'),
            Column::make(title: 'Marca', field: 'brand', width: 100, headerFilter: 'input'),
            Column::make(title: 'Modelo', field: 'model', width: 150, headerFilter: 'input'),
            Column::make(title: 'Tipo', field: 'kind', width: 100, headerFilter: 'select', headerFilterParams: ['values' => ['Móvil' => 'Móvil', 'Laptop' => 'Laptop', 'Escritorio' => 'Escritorio', 'Monitor' => 'Monitor', 'Impresora' => 'Impresora', 'Servidor' => 'Servidor', 'Otro' => 'Otro']]),
            Column::make(title: 'Modalidad', field: 'workMode', width: 120, headerFilter: 'input'),
            Column::make(title: 'Criticidad', field: 'criticality', width: 100, hozAlign: 'center', formatter: 'html'),
            Column::make(title: 'Estado', field: 'status', width: 120, hozAlign: 'center', formatter: 'html', headerFilter: 'list', headerFilterParams: ['values' => ['available' => 'Disponible', 'assigned' => 'Asignado', 'maintenance' => 'Mantenimiento', 'retired' => 'Retirado']]),
            Column::make(title: 'Adquisición', field: 'date', width: 120),
        ];
    }
}
