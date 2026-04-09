<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

final class Upsert extends Data
{
    public function __construct(
        public ?int $id,

        #[Required, Max(100)]
        public string $area,

        #[Required, Max(100)]
        public string $hostname,

        #[Required, Max(100)]
        public string $serial,

        #[Required, Max(100)]
        public string $brand,

        #[Required, Max(100)]
        public string $model,

        #[Required, Max(100)]
        public string $kind,

        #[Required, Max(50)]
        public string $status,

        #[Max(100)]
        public ?string $cpu = null,

        #[Max(50)]
        public ?string $ram = null,

        #[Max(50)]
        public ?string $ssd = null,

        #[Max(50)]
        public ?string $hdd = null,

        #[Max(100)]
        public ?string $so = null,

        #[Max(100)]
        public ?string $sap = null,

        public ?float $price = null,

        // El Cast permite que Spatie entienda el input del formulario
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?Carbon $acquisition_date = null,

        #[Max(100)]
        public ?string $invoice = null,

        #[Max(150)]
        public ?string $supplier = null,

        #[Max(100)]
        public ?string $warranty = null,

        #[Max(100)]
        public ?string $classification = null,

        public ?int $confidentiality = null,
        public ?int $integrity = null,
        public ?int $availability = null,

        #[Max(255)]
        public ?string $location = null,

        #[Max(50)]
        public ?string $phone = null,

        #[MapInputName('work_mode')] // Para asegurar coherencia con el form
        public ?string $work_mode = null,

        #[Max(255)]
        public ?string $url = null,

        #[Max(150)]
        public ?string $operator = null,
    ) {}

    /** @return list<Field> */
    public static function fields(): array
    {
        return [
            new Field(name: 'status', label: 'Estado', type: 'select', required: true, options: [
                'available' => 'Disponible',
                'assigned' => 'Asignado',
                'maintenance' => 'Mantenimiento',
                'retired' => 'Retirado',
            ], width: FieldWidth::Quarter),
            new Field(name: 'area', label: 'Área', required: true, width: FieldWidth::Quarter),
            new Field(name: 'hostname', label: 'Hostname', required: true, width: FieldWidth::Quarter),
            new Field(name: 'serial', label: 'Serial', required: true, width: FieldWidth::Quarter),
            new Field(name: 'brand', label: 'Marca', required: true, width: FieldWidth::Quarter),
            new Field(name: 'model', label: 'Modelo', required: true, width: FieldWidth::Quarter),
            new Field(name: 'kind', label: 'Tipo', required: true, width: FieldWidth::Quarter),
            new Field(name: 'classification', label: 'Clasificación', required: false, width: FieldWidth::Quarter),
            new Field(name: 'confidentiality', label: 'Conf.', type: 'number', required: false, width: FieldWidth::Quarter),
            new Field(name: 'integrity', label: 'Int.', type: 'number', required: false, width: FieldWidth::Quarter),
            new Field(name: 'availability', label: 'Disp.', type: 'number', required: false, width: FieldWidth::Quarter),
            new Field(name: 'cpu', label: 'CPU', required: false, width: FieldWidth::Quarter),
            new Field(name: 'ram', label: 'RAM', required: false, width: FieldWidth::Quarter),
            new Field(name: 'ssd', label: 'SSD', required: false, width: FieldWidth::Quarter),
            new Field(name: 'hdd', label: 'HDD', required: false, width: FieldWidth::Quarter),
            new Field(name: 'so', label: 'S.O.', required: false, width: FieldWidth::Quarter),
            new Field(name: 'sap', label: 'Código SAP', required: false, width: FieldWidth::Quarter),
            new Field(name: 'price', label: 'Precio', type: 'number', required: false, width: FieldWidth::Quarter),
            new Field(name: 'acquisition_date', label: 'Fecha Compra', type: 'date', required: false, width: FieldWidth::Quarter),
            new Field(name: 'invoice', label: 'Factura', required: false, width: FieldWidth::Quarter),
            new Field(name: 'supplier', label: 'Proveedor', required: false, width: FieldWidth::Quarter),
            new Field(name: 'warranty', label: 'Garantía', required: false, width: FieldWidth::Quarter),
            new Field(name: 'work_mode', label: 'Modalidad', required: false, width: FieldWidth::Quarter),
            new Field(name: 'location', label: 'Ubicación', required: false, width: FieldWidth::Quarter),
            new Field(name: 'phone', label: 'Teléfono', required: false, width: FieldWidth::Quarter),
            new Field(name: 'operator', label: 'Operador', required: false, width: FieldWidth::Quarter),
        ];
    }
}
