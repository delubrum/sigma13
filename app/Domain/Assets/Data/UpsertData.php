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

final class UpsertData extends Data
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
        public string $status = 'available',

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
            new Field(name: 'area', label: 'Area', type: 'select', required: true, options: [
                'IT' => 'IT',
                'Machinery' => 'Machinery',
                'Locative' => 'Locative',
                'Metrology' => 'Metrology',
                'Vehicles' => 'Vehicles',
            ], widget: 'slimselect', width: FieldWidth::Quarter),
            new Field(name: 'hostname', label: 'Hostname', required: true, width: FieldWidth::Quarter),
            new Field(name: 'serial', label: 'Serial', required: true, width: FieldWidth::Quarter),
            new Field(name: 'brand', label: 'Brand', required: true, width: FieldWidth::Quarter),
            new Field(name: 'model', label: 'Model', required: true, width: FieldWidth::Quarter),
            new Field(name: 'kind', label: 'Type', type: 'select', required: true, options: [
                'N/A' => 'N/A',
                'Access Point' => 'Access Point',
                'All-in-One' => 'All-in-One',
                'Biometric' => 'Biometric',
                'Chair' => 'Chair',
                'Crusher' => 'Crusher',
                'Desk' => 'Desk',
                'Firewall' => 'Firewall',
                'Gun' => 'Gun',
                'IP Camera' => 'IP Camera',
                'Laptop' => 'Laptop',
                'Mini Box' => 'Mini Box',
                'Mini Tower' => 'Mini Tower',
                'Mobile Phone' => 'Mobile Phone',
                'Monitor' => 'Monitor',
                'NAS' => 'NAS',
                'Printer' => 'Printer',
                'Server' => 'Server',
                'Shredder' => 'Shredder',
                'Sound Bar' => 'Sound Bar',
                'Switch' => 'Switch',
                'Tablet' => 'Tablet',
                'Tower' => 'Tower',
                'TV' => 'TV',
                'UPS' => 'UPS',
                'Video Camera' => 'Video Camera',
            ], widget: 'slimselect', width: FieldWidth::Quarter),
            new Field(name: 'classification', label: 'Classification', type: 'select', required: true, options: [
                'Confidential' => 'Confidential',
                'Restricted' => 'Restricted',
                'Internal' => 'Internal',
                'Public' => 'Public',
            ], widget: 'slimselect', width: FieldWidth::Quarter),
            new Field(name: 'confidentiality', label: 'Confidentiality', type: 'number', required: true, width: FieldWidth::Quarter),
            new Field(name: 'integrity', label: 'Integrity', type: 'number', required: true, width: FieldWidth::Quarter),
            new Field(name: 'availability', label: 'Availability', type: 'number', required: true, width: FieldWidth::Quarter),
            new Field(name: 'cpu', label: 'CPU', required: true, width: FieldWidth::Quarter),
            new Field(name: 'ram', label: 'RAM', required: true, width: FieldWidth::Quarter),
            new Field(name: 'ssd', label: 'SSD1', required: true, width: FieldWidth::Quarter),
            new Field(name: 'hdd', label: 'SSD2', required: true, width: FieldWidth::Quarter),
            new Field(name: 'so', label: 'SO', required: true, width: FieldWidth::Quarter),
            new Field(name: 'sap', label: 'SAP', required: true, width: FieldWidth::Quarter),
            new Field(name: 'price', label: 'Price', required: true, width: FieldWidth::Quarter),
            new Field(name: 'acquisition_date', label: 'Date', type: 'date', required: false, widget: 'flatpickr', width: FieldWidth::Quarter),
            new Field(name: 'invoice', label: 'Invoice', required: true, width: FieldWidth::Quarter),
            new Field(name: 'supplier', label: 'Supplier', required: true, width: FieldWidth::Quarter),
            new Field(name: 'warranty', label: 'Warranty', type: 'date', required: false, widget: 'flatpickr', width: FieldWidth::Quarter),
            new Field(name: 'work_mode', label: 'Work Mode', type: 'select', required: true, options: [
                'On-site' => 'On-site',
                'Remote' => 'Remote',
                'Hybrid' => 'Hybrid',
            ], widget: 'slimselect', width: FieldWidth::Quarter),
            new Field(name: 'location', label: 'Location', required: true, width: FieldWidth::Quarter),
            new Field(name: 'phone', label: 'Phone', required: false, width: FieldWidth::Quarter),
            new Field(name: 'operator', label: 'Operator', required: false, width: FieldWidth::Quarter),
        ];
    }
}
