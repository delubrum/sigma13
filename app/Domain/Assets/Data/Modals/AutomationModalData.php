<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Modals;

use App\Domain\Shared\Data\Field;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class AutomationModalData extends Data
{
    public function __construct(
        #[Required, Max(255)]
        #[Field(label: 'Actividad / Tarea')]
        public string $activity,

        #[Required, Max(100)]
        #[Field(label: 'Frecuencia', type: 'select', options: [
            'Diaria' => 'Diaria',
            'Semanal' => 'Semanal',
            'Mensual' => 'Mensual',
            'Trimestral' => 'Trimestral',
            'Semestral' => 'Semestral',
            'Anual' => 'Anual',
        ], widget: 'slimselect')]
        public string $frequency,

        #[Field(label: 'Fecha de Última Ejecución', type: 'date')]
        public ?string $last_performed_at = null,

        #[Field(label: 'Estado', type: 'checkbox')]
        public bool $status = true,
    ) {}
}
