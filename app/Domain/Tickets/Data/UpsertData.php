<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Data;

use App\Domain\Shared\Data\Field;
use Spatie\LaravelData\Data;

final class UpsertData extends Data
{
    public function __construct(
        #[Field(label: 'Tipo', type: 'select', options: ['HR' => 'HR', 'OHS' => 'OHS', 'Marketing' => 'Marketing'])]
        public readonly string $kind,

        #[Field(label: 'Sede/Ubicación', type: 'text', placeholder: 'Ubicación')]
        public readonly string $facility,

        #[Field(label: 'Prioridad', type: 'select', options: ['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High', 'Critical' => 'Critical'])]
        public readonly string $priority,

        #[Field(label: 'Descripción', type: 'textarea', placeholder: 'Detalle del ticket')]
        public readonly string $description,
    ) {}
}
