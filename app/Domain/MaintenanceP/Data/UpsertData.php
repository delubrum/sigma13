<?php

declare(strict_types=1);

namespace App\Domain\MaintenanceP\Data;

use App\Domain\Shared\Data\Field;
use Spatie\LaravelData\Data;

final class UpsertData extends Data
{
    public function __construct(
        #[Field(label: 'Actividad', type: 'text', placeholder: 'Nombre de la actividad')]
        public readonly string $activity,

        #[Field(label: 'Frecuencia', type: 'select', options: [
            'Weekly' => 'Semanal', 
            'Monthly' => 'Mensual', 
            'Quarterly' => 'Trimestral', 
            'Semiannual' => 'Semestral', 
            'Annual' => 'Anual'
        ])]
        public readonly string $frequency,

        #[Field(label: 'Fecha Programada', type: 'date')]
        public readonly string $scheduled_start,
    ) {}
}
