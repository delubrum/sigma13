<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Modals;

use App\Domain\Shared\Data\Field;
use Spatie\LaravelData\Data;

final class Assignment extends Data
{
    public function __construct(
        #[Field(
            label: 'Responsable', 
            type: 'select', 
            required: true, 
            placeholder: 'Seleccionar empleado',
            widget: 'slimselect'
        )]
        public readonly int $employee_id,

        #[Field(
            label: 'Hardware', 
            type: 'tags', 
            required: false, 
            placeholder: 'Selecciona o escribe el hardware'
        )]
        /** @var list<string> */
        public readonly array $hardware,

        #[Field(
            label: 'Software', 
            type: 'tags', 
            required: false, 
            placeholder: 'Selecciona o escribe el software'
        )]
        /** @var list<string> */
        public readonly array $software,

        #[Field(
            label: 'Acta / Notas', 
            type: 'textarea', 
            required: false, 
            placeholder: 'Observaciones de la asignación'
        )]
        public readonly ?string $notes,
    ) {}
}
