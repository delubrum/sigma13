<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Actions;

use App\Domain\Engineering\Models\Fastener;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetFastenerData
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $query = Fastener::query()->from('screws as a');

        $fieldMap = [
            'code' => 'a.code',
            'description' => 'a.description',
            'category' => 'a.category',
            'head' => 'a.head',
            'screwdriver' => 'a.screwdriver',
            'diameter' => 'a.diameter',
            'length' => 'a.item_length',
            'observation' => 'a.observation',
        ];

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;
            if (isset($fieldMap[$field])) {
                $query->where($fieldMap[$field], 'LIKE', "%$value%");
            }
        }

        foreach ($sorts as $field => $dir) {
            if (isset($fieldMap[$field])) {
                $query->orderBy($fieldMap[$field], $dir);
            }
        }

        if (empty($sorts)) {
            $query->orderBy('a.id', 'desc');
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        $data = collect($paginator->items())->map(function ($r) {
            $link = '';
            $directorio = "uploads/screws/$r->code/";
            if (is_dir($directorio)) {
                $archivos = array_diff(scandir($directorio), ['.', '..']);
                sort($archivos);
                foreach ($archivos as $fileName) {
                    $v = file_exists($directorio.$fileName) ? filemtime($directorio.$fileName) : time();
                    $link .= "<a href='/$directorio$fileName?v=$v' target='_blank' class='block text-blue-600 underline'>$fileName</a>";
                }
            }

            $img_path = "uploads/screws/$r->code/$r->code.png";
            $img_v = file_exists($img_path) ? filemtime($img_path) : time();

            return [
                'id' => $r->id,
                'img' => "/$img_path?v=$img_v",
                'code' => $r->code,
                'description' => $r->description,
                'category' => $r->category,
                'head' => $r->head,
                'screwdriver' => $r->screwdriver,
                'diameter' => $r->diameter,
                'length' => $r->item_length,
                'observation' => $r->observation,
                'files' => $link,
            ];
        });

        return [
            'data' => $data,
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
