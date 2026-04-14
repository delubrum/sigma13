<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Actions;

use App\Domain\Engineering\Models\TechnicalLibrary;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetLibraryData
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $query = TechnicalLibrary::query();

        $fieldMap = [
            'geometry_shape' => 'geometry_shape',
            'company' => 'company_id',
            'category' => 'category_id',
            'b' => 'b',
            'h' => 'h',
            'e1' => 'e1',
            'e2' => 'e2',
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
            $img = "uploads/matrices/{$r->id}.png";
            $v = file_exists(public_path($img)) ? filemtime(public_path($img)) : time();
            
            return [
                'id' => $r->id,
                'img' => "/$img?v=$v",
                'geometry_shape' => $r->geometry_shape,
                'company' => $r->company_id,
                'category' => $r->category_id,
                'b' => (string)$r->b,
                'h' => (string)$r->h,
                'e1' => (string)$r->e1,
                'e2' => (string)$r->e2,
                'clicks' => $r->clicks,
                'system' => $r->systema,
                'files' => '', // TODO: implement file list if needed
            ];
        });

        return [
            'data' => $data,
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }
}
