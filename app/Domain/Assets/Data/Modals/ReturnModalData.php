<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Modals;

use App\Domain\Shared\Data\Field;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class ReturnModalData extends Data
{
    public function __construct(
        /** @var array<string, string> */
        #[Required, ArrayType]
        #[Field(label: 'Estado del Hardware', widget: 'condition-grid')]
        public array $hardware,

        #[Required, BooleanType]
        #[Field(label: '¿Borrado de datos (Wipe)?', type: 'checkbox')]
        public bool $wipe,

        #[Field(label: 'Notas de Recepción', type: 'textarea')]
        public ?string $notes = null,
    ) {}
}
