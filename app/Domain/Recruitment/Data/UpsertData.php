<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Data;

final class UpsertData extends Data
{
    public function __construct(
        public readonly ?int $id,

        #[Field(label: 'Perfil de Cargo', type: 'select', placeholder: 'Seleccionar perfil...', widget: 'slimselect', width: FieldWidth::Half, route: 'global.options', routeParams: ['model' => 'job_profiles'])]
        public readonly int $profile_id,

        #[Field(
            label: 'Email Aprobador',
            type: 'email',
            placeholder: 'correo@empresa.com',
            width: FieldWidth::Half,
        )]
        public readonly string $approver,

        #[Field(
            label: 'Ciudad',
            type: 'select',
            options: [
                'ESM1' => 'ESM1 - Barranquilla',
                'ESM2' => 'ESM2 - Barranquilla',
                'Medellín' => 'Medellín',
                'Barranquilla' => 'Barranquilla',
            ],
            width: FieldWidth::Half,
        )]
        public readonly string $city,

        #[Field(
            label: 'Cantidad de Vacantes',
            type: 'number',
            placeholder: '1',
            width: FieldWidth::Quarter,
        )]
        public readonly int $qty,

        #[Field(
            label: 'Tipo de Contrato',
            type: 'select',
            options: [
                'Indefinido' => 'Indefinido',
                'Fijo' => 'Fijo',
                'Obra Labor' => 'Obra Labor',
                'Temporal' => 'Temporal',
                'Aprendizaje' => 'Aprendizaje',
            ],
            width: FieldWidth::Half,
        )]
        public readonly string $contract,

        #[Field(
            label: 'Rango Salarial',
            type: 'text',
            placeholder: 'Ej: 1.200.000 - 1.500.000',
            width: FieldWidth::Half,
        )]
        public readonly ?string $srange,

        #[Field(
            label: 'Fecha de Ingreso Esperada',
            type: 'text',
            widget: 'flatpickr',
            width: FieldWidth::Half,
        )]
        public readonly ?string $start_date,

        #[Field(
            label: 'Causa de la Vacante',
            type: 'select',
            options: [
                'Expansión' => 'Expansión / Nueva vacante',
                'Reemplazo' => 'Reemplazo',
                'Rotación' => 'Rotación',
                'Otro' => 'Otro',
            ],
            width: FieldWidth::Half,
        )]
        public readonly ?string $cause,

        #[Field(label: 'Recursos requeridos (Stage 1)', type: 'select', placeholder: 'Seleccionar recursos...', widget: 'slimselect', width: FieldWidth::Full, multiple: true, route: 'global.options', routeParams: ['model' => 'recruitment_resources', 'filter' => ['stage' => '1']])]
        public readonly ?string $resources,
    ) {}
}
