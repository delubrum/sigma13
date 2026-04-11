<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Modals;

use App\Domain\Shared\Data\UI;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class DocumentData extends Data
{
    public function __construct(
        #[Required, Min(3), Max(255)]
        #[UI(label: 'Nombre del Documento', placeholder: 'Factura, Garantía, Acta...')]
        public string $name,
        
        #[UI(label: 'Código o Referencia', placeholder: 'Código interno, id factura, etc')]
        public ?string $code = null,
        
        #[Date]
        #[UI(label: 'Vencimiento', widget: 'flatpickr', placeholder: 'YYYY-MM-DD')]
        public ?string $expiry = null,
        
        #[Required, File]
        #[UI(label: 'Archivo Principal (.pdf .jpg .zip)', widget: 'sigma-file', accept: '.pdf,.jpg,.png,.jpeg')]
        public UploadedFile $file,
    ) {}
}
