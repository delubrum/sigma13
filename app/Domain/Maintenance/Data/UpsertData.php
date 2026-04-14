<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Enums\FieldWidth;
use Spatie\LaravelData\Data;

final class UpsertData extends Data
{
    public function __construct(
        public readonly ?string $facility = null,
        public readonly ?int $asset_id = null,
        public readonly ?string $priority = null,
        public readonly ?string $description = null,
    ) {}

    public static function fields(): array
    {
        return [
            Field::make('facility', 'Sede')
                ->width(FieldWidth::Half)
                ->options(['Sede A' => 'Sede A', 'Sede B' => 'Sede B']), // Example options
            Field::make('asset_id', 'Activo')
                ->width(FieldWidth::Half)
                ->widget('slimselect')
                ->route('assets.search'), // Assuming a search route exists
            Field::make('priority', 'Prioridad')
                ->width(FieldWidth::Half)
                ->options(['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High', 'Critical' => 'Critical']),
            Field::make('description', 'Descripción')
                ->width(FieldWidth::Full)
                ->type('textarea'),
            Field::make('files', 'Evidencias')
                ->width(FieldWidth::Full)
                ->type('file')
                ->widget('filepond'),
        ];
    }
}
