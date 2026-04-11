<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Modals;

use App\Domain\Shared\Data\UI;
use App\Domain\Shared\Data\FieldWidth;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\MimeTypes;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class AssignmentData extends Data
{
    public function __construct(
        #[Required, Exists('employees', 'id')]
        #[UI(label: 'Responsable', widget: 'slimselect')]
        public int $employee_id,

        #[ArrayType]
        #[UI(label: 'Inventario de Hardware', widget: 'assets::components.widgets.hardware-list')]
        public array $hardware = [],

        #[ArrayType]
        #[UI(label: 'Software Requerido', widget: 'assets::components.widgets.software-list')]
        public array $software = [],

        #[UI(label: 'Observaciones', placeholder: 'Notas adicionales sobre la entrega...')]
        public ?string $notes = null,

        #[MimeTypes('application/pdf'), Max(10240)]
        #[UI(label: 'Acta de Entrega (PDF)', widget: 'sigma-file')]
        public mixed $file = null,
    ) {}
}
