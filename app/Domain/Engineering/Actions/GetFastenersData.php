<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Actions;

use App\Domain\Engineering\Models\Screw;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetFastenersData
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $query = Screw::query();

        $fieldMap = [
            'code' => 'code',
            'description' => 'description',
            'category' => 'category',
            'head' => 'head',
            'screwdriver' => 'screwdriver',
            'diameter' => 'diameter',
            'length' => 'item_length',
            'observation' => 'observation',
        ];

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;
            
            $dbField = $fieldMap[$field] ?? $field;
            $query->where($dbField, 'LIKE', "%$value%");
        }

        foreach ($sorts as $field => $dir) {
            $dbField = $fieldMap[$field] ?? $field;
            $query->orderBy($dbField, $dir);
        }

        if (empty($sorts)) {
            $query->orderBy('id', 'desc');
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        $data = collect($paginator->items())->map(function($r) {
            $link = '';
            $directorio = public_path("uploads/screws/{$r->code}/");
            if (is_dir($directorio)) {
                $archivos = array_diff(scandir($directorio), ['.', '..']);
                sort($archivos);
                foreach ($archivos as $fileName) {
                    $v = filemtime($directorio.$fileName);
                    $link .= "<a href='/uploads/screws/{$r->code}/$fileName?v=$v' target='_blank' class='block text-blue-600 underline'>$fileName</a>";
                }
            }

            $img_path = "uploads/screws/{$r->code}/{$r->code}.png";
            $img_v = file_exists(public_path($img_path)) ? filemtime(public_path($img_path)) : time();

            return [
                'id' => $r->id,
                'img' => "/$img_path?v=$img_v",
                'code' => (string)$r->code,
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
