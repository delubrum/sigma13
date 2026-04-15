<?php

declare(strict_types=1);

namespace App\Domain\JobProfiles\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Data;

final class UpsertData extends Data
{
    public function __construct(
        public readonly ?int $id,

        #[Field(label: 'Código', placeholder: 'Ej: JP-001', width: FieldWidth::Half)]
        public readonly string $code,

        #[Field(label: 'Nombre del cargo', placeholder: 'Ej: Analista de Calidad', width: FieldWidth::Half)]
        public readonly string $name,

        #[Field(label: 'División', widget: 'slimselect', width: FieldWidth::Half, route: 'job-profiles.options.divisions')]
        public readonly ?int $division_id,

        #[Field(label: 'Reporta a', widget: 'slimselect', width: FieldWidth::Half, route: 'job-profiles.options.positions')]
        public readonly ?int $reports_to,

        #[Field(label: 'Modalidad de trabajo', type: 'select', options: [
            'Presencial' => 'Presencial',
            'Teletrabajo' => 'Teletrabajo',
            'Remoto' => 'Remoto',
        ], width: FieldWidth::Half)]
        public readonly ?string $work_mode,

        #[Field(label: 'Nivel Jerárquico', type: 'select', options: [
            'Junta Directiva' => 'Junta Directiva',
            'Alta Dirección' => 'Alta Dirección',
            'Gerencias' => 'Gerencias',
            'Directores' => 'Directores',
            'Jefes de Área' => 'Jefes de Área',
            'Personal Administrativo' => 'Personal Administrativo',
            'Aprendices' => 'Aprendices',
            'Personal Operativo' => 'Personal Operativo',
        ], width: FieldWidth::Half)]
        public readonly ?string $rank,

        #[Field(label: 'Horario de trabajo', placeholder: 'Ej: L-V 7am-5pm', width: FieldWidth::Half)]
        public readonly ?string $schedule,

        #[Field(label: 'Disponibilidad para viajar', type: 'select', options: ['SI' => 'SI', 'NO' => 'NO'], width: FieldWidth::Quarter)]
        public readonly ?string $travel,

        #[Field(label: 'Disponibilidad cambio residencia', type: 'select', options: ['SI' => 'SI', 'NO' => 'NO'], width: FieldWidth::Quarter)]
        public readonly ?string $relocation,

        #[Field(label: 'Idiomas', type: 'select', options: [
            'N/A' => 'N/A',
            'Inglés Básico' => 'Inglés Básico',
            'Inglés Medio' => 'Inglés Medio',
            'Inglés Avanzado' => 'Inglés Avanzado',
        ], width: FieldWidth::Half)]
        public readonly ?string $lang,

        #[Field(label: 'Experiencia', placeholder: 'Ej: 2 años en cargos similares', width: FieldWidth::Half)]
        public readonly ?string $experience,

        #[Field(label: 'Convalidaciones u Observaciones', placeholder: 'Observaciones relevantes', width: FieldWidth::Full)]
        public readonly ?string $obs,

        #[Field(label: 'Misión del cargo', type: 'textarea', placeholder: 'Describa la misión principal del cargo...')]
        public readonly ?string $mission,
    ) {}
}
