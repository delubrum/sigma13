<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Actions;

use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

final class Upsert
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $route, Request $request): JsonResponse
    {
        $domain = Str::studly($route);
        $dtoClass = "App\\Domain\\{$domain}\\Data\\UpsertData";
        $modelClass = "App\\Domain\\{$domain}\\Models\\" . Str::singular($domain);

        if (! class_exists($dtoClass)) {
            abort(404, "DTO {$dtoClass} no encontrado.");
        }

        if (! class_exists($modelClass)) {
            abort(404, "Modelo {$modelClass} no encontrado.");
        }

        // Validación dinámica mediante Spatie Data con manejo de errores para HTMX
        try {
            $data = $dtoClass::validateAndCreate($request->all());
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            $this->hxNotify('Error: ' . $firstError, 'error');
            return $this->hxResponse(['errors' => $e->errors()], 422);
        }

        // Persistencia genérica
        $modelClass::updateOrCreate(['id' => $data->id], collect($data->toArray())->except('id')->toArray());

        $this->hxNotify($data->id ? 'Registro actualizado correctamente' : 'Registro creado correctamente');
        $this->hxRefreshTables(["dt_{$route}"]);
        $this->hxCloseModals(['modal-body']);

        return $this->hxResponse();
    }

    public function asController(Request $request, string $route): JsonResponse
    {
        return $this->handle($route, $request);
    }
}
