<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Modals;

use App\Domain\Shared\Data\Field;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class DocumentModalData extends Data
{
    public function __construct(
        #[Required, Max(255)]
        #[Field(label: 'Nombre del Documento')]
        public string $name,

        #[Required, Max(100)]
        #[Field(label: 'Código de Referencia')]
        public string $code,

        #[Field(label: 'Fecha de Vencimiento', type: 'date')]
        public ?string $expiry = null,

        #[Field(label: 'Archivo (PDF)', type: 'file')]
        public mixed $file = null,
    ) {}
}
