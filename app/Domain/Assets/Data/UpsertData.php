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

        #[Field(label: 'Area', type: 'select', options: [
            'IT' => 'IT',
            'Machinery' => 'Machinery',
            'Locative' => 'Locative',
            'Metrology' => 'Metrology',
            'Vehicles' => 'Vehicles',
        ], widget: 'slimselect', width: FieldWidth::Quarter)]
        #[Required, Max(100)]
        public string $area,

        #[Field(label: 'Hostname', width: FieldWidth::Quarter)]
        #[Required, Max(100)]
        public string $hostname,

        #[Field(label: 'Serial', width: FieldWidth::Quarter)]
        #[Required, Max(100)]
        public string $serial,

        #[Field(label: 'Brand', width: FieldWidth::Quarter)]
        #[Required, Max(100)]
        public string $brand,

        #[Field(label: 'Model', width: FieldWidth::Quarter)]
        #[Required, Max(100)]
        public string $model,

        #[Field(label: 'Type', type: 'select', options: [
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
        ], widget: 'slimselect', width: FieldWidth::Quarter)]
        #[Required, Max(100)]
        public string $kind,

        #[Required, Max(50)]
        public string $status = 'available',

        #[Field(label: 'CPU', width: FieldWidth::Quarter)]
        #[Max(100)]
        public ?string $cpu = null,

        #[Field(label: 'RAM', width: FieldWidth::Quarter)]
        #[Max(50)]
        public ?string $ram = null,

        #[Field(label: 'SSD1', width: FieldWidth::Quarter)]
        #[Max(50)]
        public ?string $ssd = null,

        #[Field(label: 'SSD2', width: FieldWidth::Quarter)]
        #[Max(50)]
        public ?string $hdd = null,

        #[Field(label: 'SO', width: FieldWidth::Quarter)]
        #[Max(100)]
        public ?string $so = null,

        #[Field(label: 'SAP', width: FieldWidth::Quarter)]
        #[Max(100)]
        public ?string $sap = null,

        #[Field(label: 'Price', width: FieldWidth::Quarter)]
        public ?float $price = null,

        #[Field(label: 'Date', type: 'date', widget: 'flatpickr', width: FieldWidth::Quarter)]
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?Carbon $acquisition_date = null,

        #[Field(label: 'Invoice', width: FieldWidth::Quarter)]
        #[Max(100)]
        public ?string $invoice = null,

        #[Field(label: 'Supplier', width: FieldWidth::Quarter)]
        #[Max(150)]
        public ?string $supplier = null,

        #[Field(label: 'Warranty', type: 'date', widget: 'flatpickr', width: FieldWidth::Quarter)]
        #[Max(100)]
        public ?string $warranty = null,

        #[Field(label: 'Classification', type: 'select', options: [
            'Confidential' => 'Confidential',
            'Restricted' => 'Restricted',
            'Internal' => 'Internal',
            'Public' => 'Public',
        ], widget: 'slimselect', width: FieldWidth::Quarter)]
        #[Max(100)]
        public ?string $classification = null,

        #[Field(label: 'Confidentiality', type: 'number', width: FieldWidth::Quarter)]
        public ?int $confidentiality = null,

        #[Field(label: 'Integrity', type: 'number', width: FieldWidth::Quarter)]
        public ?int $integrity = null,

        #[Field(label: 'Availability', type: 'number', width: FieldWidth::Quarter)]
        public ?int $availability = null,

        #[Field(label: 'Location', width: FieldWidth::Quarter)]
        #[Max(255)]
        public ?string $location = null,

        #[Field(label: 'Phone', width: FieldWidth::Quarter)]
        #[Max(50)]
        public ?string $phone = null,

        #[Field(label: 'Work Mode', type: 'select', options: [
            'On-site' => 'On-site',
            'Remote' => 'Remote',
            'Hybrid' => 'Hybrid',
        ], widget: 'slimselect', width: FieldWidth::Quarter)]
        #[MapInputName('work_mode')]
        public ?string $work_mode = null,

        #[Max(255)]
        public ?string $url = null,

        #[Field(label: 'Operator', width: FieldWidth::Quarter)]
        #[Max(150)]
        public ?string $operator = null,
    ) {}
}
