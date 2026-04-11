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
        public readonly string $name,
        public readonly string $code,
        public readonly string $expiry,
        public readonly string $file,
        public readonly string $actions,
    ) {}

    /** @return list<Column> */
    public static function columns(): array
    {
        return [
            Column::make(title: 'Nombre', field: 'name', width: 400, headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Código de Ref.', field: 'code', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Vencimiento', field: 'expiry', headerFilter: 'input', headerFilterPlaceholder: 'Filtro...'),
            Column::make(title: 'Archivo', field: 'file', width: 150, hozAlign: 'center', formatter: 'html'),
        ];
    }

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
