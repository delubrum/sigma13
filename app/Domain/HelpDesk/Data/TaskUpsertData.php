<?php

declare(strict_types=1);

namespace App\Domain\HelpDesk\Data;

use App\Domain\Shared\Data\Field;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Data;

final class TaskUpsertData extends Data
{
    public function __construct(
        #[Field(type: 'hidden')]
        public readonly int $issue_id,

        #[Field(
            label: 'Complejidad',
            type: 'select',
            options: ['High' => 'Alta', 'Medium' => 'Media', 'Low' => 'Baja'],
            widget: 'slimselect',
            width: FieldWidth::Full,
        )]
        public readonly string $complexity,

        #[Field(
            label: 'Atendido por',
            type: 'select',
            options: ['Internal' => 'Interno', 'External' => 'Externo'],
            widget: 'slimselect',
            width: FieldWidth::Full,
        )]
        public readonly string $action_taken,

        #[Field(label: 'Duración (minutos)', type: 'number', min: '1', width: FieldWidth::Full)]
        public readonly int $duration_minutes,

        #[Field(label: 'Notas / Observaciones', type: 'textarea', rows: 4, width: FieldWidth::Full)]
        public readonly string $notes,

        #[Field(
            label: 'Foto / Evidencia',
            type: 'file',
            widget: 'sigma-file',
            accept: 'image/*',
            width: FieldWidth::Full,
        )]
        public readonly mixed $files = null,

        public readonly ?int $id = null,
    ) {}
}
