<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Enums\FieldWidth;
use Spatie\LaravelData\Data;

final class UpsertData extends Data
{
    public function __construct(
        public readonly ?int $profile_id = null,
        public readonly ?string $approver = null,
        public readonly ?string $city = null,
        public readonly ?int $qty = null,
        public readonly ?string $contract = null,
        public readonly ?string $srange = null,
        public readonly ?string $start_date = null,
        public readonly ?string $cause = null,
        public readonly ?string $replaces = null,
        public readonly ?string $others = null,
    ) {}

    public static function fields(): array
    {
        return [
            Field::make('profile_id', 'Perfil de Cargo')
                ->width(FieldWidth::Half)
                ->widget('slimselect')
                ->route('recruitment.profiles.search'),
            Field::make('approver', 'Aprobador (Email)')
                ->width(FieldWidth::Half)
                ->type('email'),
            Field::make('city', 'Ciudad')
                ->width(FieldWidth::Half)
                ->options(['Barranquilla' => 'Barranquilla', 'Bogotá' => 'Bogotá', 'Medellín' => 'Medellín']),
            Field::make('qty', 'Cantidad')
                ->width(FieldWidth::Half)
                ->type('number'),
            Field::make('contract', 'Tipo de Contrato')
                ->width(FieldWidth::Half)
                ->options(['Término Fijo' => 'Término Fijo', 'Término Indefinido' => 'Término Indefinido', 'Obra o Labor' => 'Obra o Labor']),
            Field::make('srange', 'Rango Salarial')
                ->width(FieldWidth::Half),
            Field::make('start_date', 'Fecha Sugerida Inicio')
                ->width(FieldWidth::Half)
                ->widget('flatpickr'),
            Field::make('cause', 'Motivo de Vacante')
                ->width(FieldWidth::Half)
                ->options(['Nueva Creación' => 'Nueva Creación', 'Reemplazo' => 'Reemplazo', 'Incremento' => 'Incremento']),
            Field::make('replaces', '¿A quién reemplaza?')
                ->width(FieldWidth::Half),
            Field::make('others', 'Observaciones Adicionales')
                ->width(FieldWidth::Full)
                ->type('textarea'),
            Field::make('file', 'Perfil de Cargo / Justificación (ZIP)')
                ->width(FieldWidth::Full)
                ->type('file')
                ->widget('filepond'),
        ];
    }
}
