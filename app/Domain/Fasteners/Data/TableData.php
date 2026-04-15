<?php

declare(strict_types=1);

namespace App\Domain\Fasteners\Data;

use App\Domain\Fasteners\Models\Fastener;
use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'Img', width: 80, hozAlign: 'center', headerSort: false, formatter: 'html')]
        public string $img,

        #[Column(title: 'ID', width: 60, hozAlign: 'center')]
        public int $id,

        #[Column(title: 'Code', width: 120, headerFilter: 'input')]
        public string $code,

        #[Column(title: 'Description', width: 250, headerFilter: 'input')]
        public string $description,

        #[Column(title: 'Category', width: 150, headerFilter: 'input')]
        public string $category,

        #[Column(title: 'Head', width: 120, headerFilter: 'input')]
        public string $head,

        #[Column(title: 'Screwdriver', width: 120, headerFilter: 'input')]
        public string $screwdriver,

        #[Column(title: 'Diameter', width: 100, headerFilter: 'input')]
        public string $diameter,

        #[Column(title: 'Length', width: 100, headerFilter: 'input')]
        public string $length,

        #[Column(title: 'Observation', width: 200, headerFilter: 'input')]
        public ?string $observation,

        #[Column(title: 'Files', width: 150, headerSort: false, formatter: 'html')]
        public string $files,
    ) {}

    public static function fromModel(Fastener $f): self
    {
        $code = $f->code ?? 'unknown';
        $dir = "uploads/screws/{$code}/";
        $img = "uploads/screws/{$code}/{$code}.png";
        $imgFullPath = public_path($img);
        
        $imgUrl = '';
        if (file_exists($imgFullPath)) {
            $mtime = filemtime($imgFullPath);
            $imgUrl = asset($img) . '?v=' . ($mtime ?: time());
        }
        $imgHtml = $imgUrl ? "<img src=\"{$imgUrl}\" class=\"w-10 h-10 object-contain mx-auto\" />" : '';

        $fileLinks = '';
        $fullPathDir = public_path($dir);
        if (is_dir($fullPathDir)) {
            $archivos = array_diff(scandir($fullPathDir) ?: [], ['.', '..']);
            sort($archivos);
            foreach ($archivos as $fileName) {
                $fileLinks .= "<a href=\"" . asset($dir.$fileName) . "\" target=\"_blank\" class=\"block text-[10px] text-blue-600 underline truncate\">{$fileName}</a>";
            }
        }

        return new self(
            img: $imgHtml,
            id: (int) $f->id,
            code: $f->code ?? '',
            description: $f->description ?? '',
            category: $f->category ?? '',
            head: $f->head ?? '',
            screwdriver: $f->screwdriver ?? '',
            diameter: $f->diameter ?? '',
            length: $f->item_length ?? '',
            observation: $f->observation,
            files: $fileLinks,
        );
    }
}
