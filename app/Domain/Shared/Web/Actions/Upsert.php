<?php

declare(strict_types=1);

namespace App\Domain\Shared\Web\Actions;

use App\Support\HtmxOrchestrator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\Data;

final class Upsert
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(string $route, Request $request): JsonResponse
    {
        $domain = Str::studly($route);
        $dtoClass = "App\\Domain\\{$domain}\\Data\\UpsertData";
        $modelClass = "App\\Domain\\{$domain}\\Models\\".Str::singular($domain);

        if (! class_exists($dtoClass)) {
            abort(404, "DTO {$dtoClass} no encontrado.");
        }

        if (! class_exists($modelClass)) {
            abort(404, "Modelo {$modelClass} no encontrado.");
        }

        // Validación dinámica mediante Spatie Data con manejo de errores para HTMX
        try {
            /** @var Data $data */
            $data = $dtoClass::validateAndCreate($request->all());
        } catch (ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            $errorMsg = is_string($firstError) ? $firstError : 'Validación fallida';
            $this->hxNotify('Error: '.$errorMsg, 'error');

            return $this->hxResponse(['errors' => $e->errors()], 422);
        }

        // Persistencia genérica
        $attributes = $data->toArray();
        $id = $attributes['id'] ?? null;
        unset($attributes['id']);

        /** @var class-string<Model> $modelClass */
        $modelClass::updateOrCreate(['id' => $id], $attributes);

        $this->hxNotify($id ? 'Registro actualizado correctamente' : 'Registro creado correctamente');
        $this->hxRefreshTables(["dt_{$route}"]);
        $this->hxCloseModals(['modal-body']);

        return $this->hxResponse();
    }

    public function asController(Request $request, string $route): JsonResponse
    {
        return $this->handle($route, $request);
    }
}
