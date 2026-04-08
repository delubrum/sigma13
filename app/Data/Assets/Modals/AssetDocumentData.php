<?php

declare(strict_types=1);

namespace App\Data\Assets\Modals;

use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Data;
use Illuminate\Http\UploadedFile;

final class AssetDocumentData extends Data
{
    public function __construct(
        public string $name,
        public ?string $code = null,
        #[Date]
        public ?string $expiry = null,
        public ?UploadedFile $file = null,
    ) {}
}
