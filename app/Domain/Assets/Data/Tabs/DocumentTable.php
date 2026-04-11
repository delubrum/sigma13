<?php

declare(strict_types=1);

namespace App\Domain\Assets\Data\Tabs;

use App\Domain\Assets\Models\AssetDocument;
use App\Domain\Shared\Data\Column;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

final class DocumentTable extends Data
{
    public function __construct(
        public readonly int $id,

        #[Column(title: 'Nombre', width: 400)]
        public readonly string $name,

        #[Column(title: 'Código de Ref.')]
        public readonly string $code,

        #[Column(title: 'Vencimiento')]
        public readonly string $expiry,

        #[Column(title: 'Archivo', width: 150, hozAlign: 'center', formatter: 'html')]
        public readonly string $file,

        public readonly string $actions,
    ) {}

    public static function fromModel(AssetDocument $doc): self
    {
        $expiryDate = $doc->expiry instanceof Carbon
            ? $doc->expiry->format('d/m/Y')
            : '---';

        $media = $doc->getFirstMedia('documents');
        $url = $media ? route('shared.media.download', $media->id) : null;

        if (! $url && $doc->url) {
            $url = $doc->url;
        }

        $fileLink = $url
            ? '<a href="'.e($url).'" target="_blank" class="text-slate-900 font-bold hover:underline"><i class="ri-external-link-line mr-1 text-slate-400"></i>Ver Documento</a>'
            : '<span class="text-gray-400">---</span>';

        return new self(
            id: $doc->id,
            name: $doc->name ?? '---',
            code: $doc->code ?? '---',
            expiry: $expiryDate,
            file: $fileLink,
            actions: '',
        );
    }
}
