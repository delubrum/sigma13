<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Actions;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class Delete
{
    use AsAction;
    use HtmxOrchestrator;

    /**
     * Delete a model record based on the provided route and ID.
     */
    public function handle(string $route, string $id, string $domain, string $modelName): JsonResponse
    {
        $class = "App\\Domain\\{$domain}\\Models\\{$modelName}";
        
        // Intentar fallback si el nombre del modelo es igual al dominio
        if (! class_exists($class)) {
            $class = "App\\Domain\\{$domain}\\Models\\{$domain}";
        }

        if (! class_exists($class)) {
            return $this->hxNotify("Modelo {$class} no encontrado.", 'error')->hxResponse();
        }

        $model = $class::findOrFail($id);
        $model->delete();

        $this->hxNotify('Registro eliminado correctamente');
        
        // Refrescamos tablas y áreas comunes
        $this->hxRefreshTables();
        $this->hxRefresh(['#media-content', '#gallery-content']);

        return $this->hxResponse();
    }

    public function asController(Request $request, string $route, string $id): JsonResponse
    {
        // El parámetro $route viene de la URL, lo transformamos a Studly Case para el Dominio
        // Ej: 'assets-documents' -> AssetsDocuments
        $parts = explode('-', $route);
        $domain = Str::studly($parts[0]);
        $modelName = isset($parts[1]) ? Str::studly(Str::singular($parts[1])) : Str::singular($domain);

        return $this->handle($route, $id, $domain, $modelName);
    }
}
