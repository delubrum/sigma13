<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Actions;

use App\Domain\Engineering\Models\Matrix;
use Illuminate\Support\Facades\File;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetEngineeringData
{
    use AsAction;

    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $query = Matrix::query();

        foreach ($filters as $field => $value) {
            if (empty($value)) continue;

            $dbField = $this->mapField($field);
            
            if (in_array($field, ['company', 'category', 'b', 'h', 'e1', 'e2'])) {
                $query->where($dbField, $value);
            } else {
                $query->where($dbField, 'LIKE', "%$value%");
            }
        }

        foreach ($sorts as $field => $dir) {
            $query->orderBy($this->mapField($field), $dir);
        }

        if (empty($sorts)) {
            $query->orderBy('id', 'desc');
        }

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        $data = collect($paginator->items())->map(function (Matrix $matrix) {
            $clicks = is_array($matrix->clicks) ? implode('<br>', $matrix->clicks) : '';
            $system = is_array($matrix->systema) ? implode('<br>', $matrix->systema) : '';

            $filesLinks = '';
            $directory = public_path("uploads/matrices/{$matrix->geometry_shape}/");
            if (File::isDirectory($directory)) {
                $files = collect(File::files($directory))
                    ->sortBy(fn($file) => $file->getFilename());

                foreach ($files as $file) {
                    $fileName = $file->getFilename();
                    if ($fileName === "{$matrix->geometry_shape}.png") continue;
                    
                    $v = $file->getMTime();
                    $url = asset("uploads/matrices/{$matrix->geometry_shape}/$fileName?v=$v");
                    $filesLinks .= "<a target='_blank' href='$url' class='block text-blue-600 underline'>$fileName</a>";
                }
            }

            $imgPath = "uploads/matrices/{$matrix->geometry_shape}/{$matrix->geometry_shape}.png";
            $imgV = File::exists(public_path($imgPath)) ? File::lastModified(public_path($imgPath)) : time();
            $imgUrl = asset("$imgPath?v=$imgV");

            return [
                'id' => $matrix->id,
                'img' => $imgUrl,
                'geometry_shape' => $matrix->geometry_shape,
                'company' => $matrix->company_id,
                'category' => $matrix->category_id,
                'b' => $matrix->b,
                'h' => $matrix->h,
                'e1' => $matrix->e1,
                'e2' => $matrix->e2,
                'clicks' => $clicks,
                'system' => $system,
                'files' => $filesLinks,
            ];
        });

        return [
            'data' => $data,
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    private function mapField(string $field): string
    {
        return match ($field) {
            'company' => 'company_id',
            'category' => 'category_id',
            'system' => 'systema',
            default => $field,
        };
    }
}
