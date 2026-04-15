<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Data;

final class CandidateUpsertData extends Data
{
    public function __construct(
        public readonly ?int $id,

        #[Field(label: 'Tipo', type: 'select', options: ['Externo' => 'Externo', 'Interno' => 'Interno', 'Referido' => 'Referido'], width: FieldWidth::Half)]
        public readonly string $kind,

        #[Field(label: 'Nombre Completo', type: 'text', placeholder: 'Nombre y apellidos', width: FieldWidth::Half)]
        public readonly string $name,

        #[Field(label: 'Cédula (CC)', type: 'text', placeholder: '1234567890', width: FieldWidth::Half)]
        public readonly string $cc,

        #[Field(label: 'Email', type: 'email', placeholder: 'candidato@email.com', width: FieldWidth::Half)]
        public readonly string $email,

        #[Field(label: 'Teléfono', type: 'text', placeholder: '300 000 0000', width: FieldWidth::Half)]
        public readonly ?string $phone,

        #[Field(label: 'Fuente CV', type: 'select', options: ['LinkedIn' => 'LinkedIn', 'Computrabajo' => 'Computrabajo', 'Referido' => 'Referido', 'Directo' => 'Directo', 'Otro' => 'Otro'], width: FieldWidth::Half)]
        public readonly ?string $cv_source,

        #[Field(label: 'Psicométricas', type: 'select', options: ['None' => 'Ninguna', 'CISD' => 'DISC', 'PF' => '16PF', 'Both' => 'Ambas'], width: FieldWidth::Half)]
        public readonly ?string $psychometrics,

        #[Field(label: 'Reclutador', type: 'select', placeholder: 'Seleccionar...', widget: 'slimselect', width: FieldWidth::Half, route: 'global.options', routeParams: ['model' => 'users'])]
        public readonly ?int $recruiter_id,

        #[Field(label: 'Fecha Cita', type: 'text', widget: 'flatpickr', width: FieldWidth::Half)]
        public readonly ?string $appointment,

        #[Field(label: 'Modalidad Cita', type: 'select', options: ['Presencial' => 'Presencial', 'Virtual' => 'Virtual'], width: FieldWidth::Half)]
        public readonly ?string $appointment_mode,

        #[Field(label: 'Sede / Ubicación', type: 'select', options: ['ESM1' => 'ESM1', 'ESM2' => 'ESM2'], width: FieldWidth::Half)]
        public readonly ?string $appointment_location,

        #[Field(label: 'Link Teams', type: 'text', placeholder: 'https://teams.microsoft.com/...', width: FieldWidth::Full)]
        public readonly ?string $teams_link,

        #[Field(label: 'Instrucciones Adicionales', type: 'textarea', placeholder: 'Indicaciones para el candidato...', width: FieldWidth::Full)]
        public readonly ?string $additional_instructions,
    ) {}
}
