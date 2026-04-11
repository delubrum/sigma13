<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Modals;

use App\Domain\Shared\Data\Field;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\MimeTypes;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class ReturnData extends Data
{
    public function __construct(
        #[Required, ArrayType]
        #[Field(label: 'Estado del Hardware', widget: 'assets::components.widgets.condition-grid')]
        public array $hardware = [],

        #[Required]
        #[Field(label: 'Borrado Seguro de Datos', widget: 'select', options: [
            'Yes' => 'Sí, realizado',
            'No' => 'No realizado',
            'N/A' => 'No aplica',
        ])]
        public string $wipe = 'No',

        #[Field(label: 'Estado General / Observaciones', placeholder: 'Describa cualquier detalle adicional sobre el retorno...')]
        public ?string $notes = null,

        #[Required, MimeTypes('application/pdf'), Max(10240)]
        #[Field(label: 'Acta de Retorno (PDF)', widget: 'sigma-file')]
        public mixed $file = null,
    ) {}
}
