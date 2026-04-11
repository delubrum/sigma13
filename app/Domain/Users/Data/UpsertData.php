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
        public ?string $name = null,

        #[Required, Email, Max(255)]
        public ?string $email = null,

        #[Max(50)]
        public ?string $document = null,

        /** @var array<int, string> */
        public array $permissions = [],

        #[Required]
        public bool $is_active = true,
    ) {}

    /** @return list<Field> */
    public static function fields(): array
    {
        return [
            new Field(name: 'name', label: 'Nombre Completo', required: true, placeholder: 'Ej: Juan Pérez'),
            new Field(name: 'email', label: 'Correo Electrónico', type: 'email', required: true, placeholder: 'juan@ejemplo.com'),
            new Field(name: 'document', label: 'Número de Documento', required: true, placeholder: 'Cédula o ID'),
            new Field(name: 'is_active', label: 'Usuario Activo', type: 'select', required: true, options: [
                true => 'Activo',
                false => 'Inactivo'
            ]),
        ];
    }
}
