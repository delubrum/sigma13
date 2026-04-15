<?php

declare(strict_types=1);

namespace App\Domain\Printing\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Data;

final class UpsertData extends Data
{
    /** @return list<Field> */
    public static function fields(): array
    {
        return [
            Field::make('es_id', 'ESWindows ID')
                ->width(FieldWidth::Full),

            (new Field(
                name: 'excel_file',
                label: '* Excel File (.xlsx)',
                type: 'file',
                required: true,
                widget: 'sigma-file',
                width: FieldWidth::Full,
                accept: '.xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            )),

            (new Field(
                name: 'qr_image',
                label: '* QR Image',
                type: 'file',
                required: true,
                widget: 'sigma-file',
                width: FieldWidth::Full,
                accept: 'image/*',
            )),
        ];
    }
}
