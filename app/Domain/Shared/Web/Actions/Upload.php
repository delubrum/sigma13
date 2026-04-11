<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Actions;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class Upload
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $route, string $id, string $domain, string $modelName): JsonResponse
    {
        $class = "App\\Domain\\{$domain}\\Models\\{$modelName}";
        if (! class_exists($class)) {
            $class = "App\\Domain\\{$domain}\\Models\\{$domain}";
        }

        if (! class_exists($class)) {
            App::abort(404, "Modelo {$class} no encontrado.");
        }

        $model = $class::findOrFail($id);
        
        if (! ($model instanceof \Spatie\MediaLibrary\HasMedia)) {
            $this->hxNotify('El modelo no soporta archivos multimedia.', 'error');
            return $this->hxResponse();
        }

        $files = request()->file('files') ?: (request()->file('file') ? [request()->file('file')] : []);
        $collection = request()->input('collection', 'gallery');

        if (!empty($files)) {
            foreach ($files as $file) {
                $model->addMedia($file)->toMediaCollection($collection);
            }
        }

        $this->hxNotify('Archivo(s) subido(s) con éxito');
        $this->hxRefresh(['#gallery-content', '#media-content', '#asset_photo_preview']);
        $this->hxTriggers['refresh-sidebar-photo'] = true;

        return $this->hxResponse();
    }

    public function asController(Request $request, string $route, string $id): JsonResponse
    {
        $domain = Str::studly($route);
        $modelName = Str::singular($domain);

        return $this->handle($route, $id, $domain, $modelName);
    }
}
