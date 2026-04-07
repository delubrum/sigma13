<?php

declare(strict_types=1);

namespace App\Actions\Shared;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\MediaLibrary\InteractsWithMedia;

final class Upload
{
    use AsAction;

    public function handle(string $route, int $id, UploadedFile $photo): string
    {
        $model = $this->resolveModel($route);
        $record = $model::findOrFail($id);

        if (! in_array(InteractsWithMedia::class, class_uses_recursive($record), true)) {
            abort(404, 'Este modelo no soporta uploads.');
        }

        $record->addMedia($photo)
            ->toMediaCollection('profile');

        return $record->getFirstMediaUrl('profile');
    }

    public function asController(Request $request, string $route, int $id): Response
    {
        $url = $this->handle($route, $id, $request->file('photo'));

        return response($url, 200);
    }

    /** @return class-string */
    private function resolveModel(string $route): string
    {
        $class = 'App\\Models\\'.Str::studly(Str::singular($route));

        if (! class_exists($class)) {
            abort(404, "Modelo no encontrado para: {$route}");
        }

        return $class;
    }
}
