<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Data;

final class UpsertData extends Data
{
    public function __construct(
        public readonly ?int $id,

        #[Field(label: 'Fecha', widget: 'flatpickr', width: FieldWidth::Half)]
        public readonly string $date,

        #[Field(label: 'Tipo', type: 'select', options: [
            'Mejora' => 'Mejora',
            'Correctiva' => 'Correctiva',
            'Preventiva' => 'Preventiva',
        ], width: FieldWidth::Half)]
        public readonly string $type,

        #[Field(label: 'Fuente', type: 'select', options: [
            'Auditoria' => 'Auditoria',
            'Producción' => 'Producción',
            'Calidad' => 'Calidad',
            'Seguridad' => 'Seguridad',
            'Cliente' => 'Cliente',
            'Otras' => 'Otras',
        ], width: FieldWidth::Half)]
        public readonly string $source,

        #[Field(label: '¿Cuál fuente?', placeholder: 'Especifique la fuente', width: FieldWidth::Half)]
        public readonly ?string $source_other,

        #[Field(label: 'Responsable', widget: 'slimselect', width: FieldWidth::Half, route: 'users.options')]
        public readonly ?int $responsible_id,

        #[Field(label: 'Proceso', placeholder: 'Ej: Laminación', width: FieldWidth::Half)]
        public readonly ?string $process,

        #[Field(label: 'Perspectiva', placeholder: 'Ej: Calidad', width: FieldWidth::Half)]
        public readonly ?string $perspective,

        #[Field(label: '¿Acción repetida?', type: 'select', options: ['1' => 'Sí', '0' => 'No'], width: FieldWidth::Half)]
        public readonly ?string $repeated,

        #[Field(label: 'Descripción', type: 'textarea', placeholder: 'Describa la situación en detalle...')]
        public readonly string $description,

        #[Field(label: 'Acción inmediata', type: 'textarea', placeholder: 'Acción tomada de inmediato...')]
        public readonly ?string $immediate_action,
    ) {}
}
