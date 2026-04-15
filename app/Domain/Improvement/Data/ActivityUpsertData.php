<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Data;

final class ActivityUpsertData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $improvement_id,

        #[Field(label: 'Actividad / Acción', type: 'textarea', placeholder: '¿Qué se va a hacer?')]
        public readonly string $action,

        #[Field(label: 'Cómo realizarlo', type: 'textarea', placeholder: '¿Cómo se va a ejecutar?')]
        public readonly string $how_to,

        #[Field(label: 'Responsable', widget: 'slimselect', width: FieldWidth::Half, route: 'users.options')]
        public readonly ?int $responsible_id,

        #[Field(label: 'Fecha límite', widget: 'flatpickr', width: FieldWidth::Half)]
        public readonly ?string $whenn,
    ) {}
}
