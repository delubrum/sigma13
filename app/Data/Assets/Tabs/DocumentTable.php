<?php

declare(strict_types=1);

namespace App\Data\Assets\Tabs;

use App\Models\AssetDocument;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

final class DocumentTable extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $code,
        public readonly string $expiry,
        public readonly string $file,
    ) {}

    public static function fromModel(AssetDocument $doc): self
    {
        $expiryDate = $doc->expiry instanceof Carbon
            ? $doc->expiry->format('d/m/Y')
            : '---';

        $fileLink = $doc->url 
            ? '<a href="'.e($doc->url).'" target="_blank" class="text-blue-500 font-bold hover:underline"><i class="ri-file-download-line mr-1"></i>Descargar</a>'
            : '<span class="text-gray-400">Sin archivo</span>';

        return new self(
            id: $doc->id,
            name: $doc->name ?? '---',
            code: $doc->code ?? '---',
            expiry: $expiryDate,
            file: $fileLink,
        );
    }
}
