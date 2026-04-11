<?php

declare(strict_types=1);

namespace App\Domain\ItMaintenance\Data;

use App\Domain\Shared\Data\Field;
use Spatie\LaravelData\Data;

final class UpsertData extends Data
{
    public function __construct(
        #[Field(label: 'Activo (ID)', type: 'number', placeholder: 'ID del Activo')]
        public readonly ?int $asset_id,

        #[Field(label: 'Prioridad', type: 'select', options: ['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High', 'Critical' => 'Critical'])]
        public readonly string $priority,

        #[Field(label: 'Sede/Ubicación', type: 'text', placeholder: 'Ubicación')]
        public readonly string $facility,

        #[Field(label: 'Descripción', type: 'textarea', placeholder: 'Detalle del problema')]
        public readonly string $description,
    ) {}
}
