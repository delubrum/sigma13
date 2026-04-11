<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Modals;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class AssetDocumentData extends Data
{
    public function __construct(
        #[Required, Min(3), Max(255)]
        public string $name,
        
        public ?string $code = null,
        
        #[Date]
        public ?string $expiry = null,
        
        #[Required, File]
        public UploadedFile $file,
    ) {}
}
