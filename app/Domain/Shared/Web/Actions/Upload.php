<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Support\DomainResolver;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\MediaLibrary\InteractsWithMedia;

final class Upload
{
    use AsAction;

    public function handle(string $route, int $id, UploadedFile $file, string $collection): string
    {
        $domain    = DomainResolver::fromRoute($route);
        $modelName = Str::studly(Str::singular($route));
        $class     = "App\\Domain\\{$domain}\\Models\\{$modelName}";

        if (! class_exists($class)) {
            $class = "App\\Domain\\{$domain}\\Models\\{$domain}";
            if (! class_exists($class)) {
                abort(404, "Modelo no encontrado para: {$route}");
            }
        }

        $record = $class::findOrFail($id);

        if (! in_array(InteractsWithMedia::class, class_uses_recursive($record), true)) {
            abort(422, 'Este modelo no soporta uploads.');
        }

        $record->addMedia($file)->toMediaCollection($collection);

        return $record->getFirstMediaUrl($collection);
    }

    public function asController(Request $request, string $route, int $id): JsonResponse
    {
        $file = $request->file('file') ?? $request->file('photo');

        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            abort(422, 'Archivo inválido.');
        }

        $collection = $request->input('collection', 'default');
        $url        = $this->handle($route, $id, $file, $collection);

        return response()->json(['url' => $url]);
    }
}
