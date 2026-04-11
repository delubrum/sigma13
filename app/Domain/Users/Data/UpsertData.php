<?php

declare(strict_types=1);

namespace App\Domain\Users\Data;

use App\Domain\Shared\Data\Field;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class UpsertData extends Data
{
    public function __construct(
        public ?int $id = null,

        #[Required, Max(255)]
        #[Field(label: 'Nombre Completo', placeholder: 'Ej: Juan Pérez')]
        public ?string $name = null,

        #[Required, Email, Max(255)]
        #[Field(label: 'Correo Electrónico', type: 'email', placeholder: 'juan@ejemplo.com')]
        public ?string $email = null,

        #[Max(50)]
        #[Field(label: 'Número de Documento', placeholder: 'Cédula o ID')]
        public ?string $document = null,

        /** @var array<int, string> */
        public array $permissions = [],

        #[Required]
        #[Field(
            label: 'Usuario Activo', 
            type: 'select', 
            options: [
                ['value' => true, 'label' => 'Activo'],
                ['value' => false, 'label' => 'Inactivo']
            ]
        )]
        public bool $is_active = true,
    ) {}
}