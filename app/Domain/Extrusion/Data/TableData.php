<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Data;

use App\Domain\Shared\Data\Column;
use Spatie\LaravelData\Data;

final class TableData extends Data
{
    public function __construct(
        #[Column(title: 'Img',      width: 80,  hozAlign: 'center', headerSort: false, formatter: 'html')]
        public readonly string $img,

        #[Column(title: 'Shape',    width: 160, headerFilter: 'input')]
        public readonly string $geometry_shape,

        #[Column(title: 'Company',  width: 120, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $company,

        #[Column(title: 'Category', width: 140, hozAlign: 'center', headerFilter: 'input')]
        public readonly string $category,

        #[Column(title: 'B',        width: 80,  hozAlign: 'center', headerFilter: 'input')]
        public readonly string $b,

        #[Column(title: 'H',        width: 80,  hozAlign: 'center', headerFilter: 'input')]
        public readonly string $h,

        #[Column(title: 'E1',       width: 80,  hozAlign: 'center', headerFilter: 'input')]
        public readonly string $e1,

        #[Column(title: 'E2',       width: 80,  hozAlign: 'center', headerFilter: 'input')]
        public readonly string $e2,

        #[Column(title: 'Clicks',   width: 160, formatter: 'html',  headerFilter: 'input')]
        public readonly string $clicks,

        #[Column(title: 'System',   width: 160, formatter: 'html',  headerFilter: 'input')]
        public readonly string $system,

        #[Column(title: 'Files',    width: 180, formatter: 'html',  headerSort: false)]
        public readonly string $files,
    ) {}

    public static function fromModel(mixed $row): self
    {
        /** @var object $row */
        $shape   = (string) ($row->geometry_shape ?? '');
        $imgPath = "storage/matrices/{$shape}/{$shape}.png";
        $img     = file_exists(public_path("storage/matrices/{$shape}/{$shape}.png"))
            ? "<img src=\"/{$imgPath}?v=" . filemtime(public_path("storage/matrices/{$shape}/{$shape}.png")) . "\" class=\"w-16 h-16 object-contain mx-auto\" />"
            : '';

        $clicksArr = is_array($row->clicks) ? $row->clicks : (json_decode((string) ($row->clicks ?? '[]'), true) ?? []);
        $systemArr = is_array($row->systema) ? $row->systema : (json_decode((string) ($row->systema ?? '[]'), true) ?? []);

        // Files links
        $files   = '';
        $dir     = public_path("storage/matrices/{$shape}");
        if (is_dir($dir)) {
            $all = array_diff((array) scandir($dir), ['.', '..']);
            sort($all);
            foreach ($all as $f) {
                $fStr = (string) $f;
                if (str_ends_with(strtolower($fStr), '.png')) {
                    continue;
                }
                $v = filemtime($dir . DIRECTORY_SEPARATOR . $fStr);
                $files .= "<a target='_blank' href='/storage/matrices/{$shape}/{$fStr}?v={$v}' class='block text-blue-500 hover:underline text-xs'>{$fStr}</a>";
            }
        }

        return new self(
            img:           $img,
            geometry_shape: $shape,
            company:       (string) ($row->company_id ?? ''),
            category:      (string) ($row->category_id ?? ''),
            b:             (string) ($row->b ?? ''),
            h:             (string) ($row->h ?? ''),
            e1:            (string) ($row->e1 ?? ''),
            e2:            (string) ($row->e2 ?? ''),
            clicks:        implode('<br>', array_filter($clicksArr, static fn ($v) => ! blank($v))),
            system:        implode('<br>', array_filter($systemArr, static fn ($v) => ! blank($v))),
            files:         $files,
        );
    }
}
