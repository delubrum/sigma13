<?php

declare(strict_types=1);

namespace App\Domain\Users\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class Table extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $document,
        #[MapInputName('is_active')]
        public readonly string $isActive,
        #[MapInputName('created_at')]
        public readonly string $createdAt,
    ) {}

    /** @return list<Column> */
    public static function columns(): array
    {
        return [
            Column::make(title: 'ID', field: 'id', width: 70, hozAlign: 'center', headerFilter: 'input'),
            Column::make(title: 'Nombre', field: 'name', headerFilter: 'input'),
            Column::make(title: 'Cédula', field: 'document', headerFilter: 'input'),
            Column::make(title: 'Email', field: 'email', headerFilter: 'input'),
            Column::make(title: 'Estado', field: 'isActive', width: 110, hozAlign: 'center', formatter: 'html', headerFilter: 'list', headerFilterParams: ['values' => [true => 'Activo', false => 'Inactivo'], 'clearable' => true]),
            Column::make(title: 'Creado', field: 'createdAt', width: 140, hozAlign: 'center', headerFilter: 'input'),
        ];
    }
}
