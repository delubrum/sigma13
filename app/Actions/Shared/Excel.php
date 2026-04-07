<?php

declare(strict_types=1);

namespace App\Actions\Shared;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\Data;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\HttpFoundation\Response;

final class Excel
{
    use AsAction;

    /**
     * @param  string  $route  Nombre del módulo/ruta (ej: 'assets')
     * @param  string  $range  Rango de tiempo ('today', 'week', 'month', 'all')
     */
    public function handle(string $route, string $range): Response
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = $this->resolveModel($route);

        /** @var class-string<Data> $dataClass */
        $dataClass = $this->resolveDataClass($route);

        /** @var Builder<Model> $query */
        $query = $modelClass::query();

        // Aplicar filtro de rango si el modelo usa Timestamps
        $query->when($range !== 'all', function (Builder $q) use ($range): void {
            $date = match ($range) {
                'today' => today(),
                'week' => now()->startOfWeek(),
                'month' => now()->startOfMonth(),
                default => now()->subYears(100),
            };
            $q->where('created_at', '>=', $date);
        });

        $fileName = sprintf('export_%s_%s.xlsx', $route, now()->format('d-m-Y_His'));

        // Creamos el stream para ahorrar memoria RAM
        $writer = SimpleExcelWriter::streamDownload($fileName);

        // BLINDAJE: Limpiar cualquier buffer previo para evitar ERR_INVALID_RESPONSE
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Cursor para procesar registros uno a uno (eficiencia O(1) en memoria)
        $query->cursor()->each(function (Model $model) use ($writer, $dataClass): void {
            /** @var Data $dataInstance */
            $dataInstance = $dataClass::from($model);

            /** @var array<string, mixed> $rowData */
            $rowData = $dataInstance->toArray();

            // Limpieza de HTML y entidades para Excel
            $cleanRow = array_map(function (mixed $value): mixed {
                if (is_string($value)) {
                    return html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }

                return $value;
            }, $rowData);

            $writer->addRow($cleanRow);
        });

        /** @var Response $response */
        $response = $writer->toBrowser();

        return $response;
    }

    /**
     * Punto de entrada desde la ruta web.
     */
    public function asController(Request $request, string $route): Response
    {
        /** @var string $range */
        $range = $request->query('range', 'all');

        return $this->handle($route, $range);
    }

    /**
     * Resuelve el nombre de la clase del Modelo basado en la ruta.
     *
     * @return class-string<Model>
     */
    private function resolveModel(string $route): string
    {
        $modelName = Str::studly(Str::singular($route));
        /** @var class-string<Model> $class */
        $class = "App\\Models\\{$modelName}";

        if (! class_exists($class)) {
            abort(404, "El modelo [{$class}] no existe.");
        }

        return $class;
    }

    /**
     * Resuelve el Data Object siguiendo la convención App\Data\{Folder}\Table.
     *
     * @return class-string<Data>
     */
    private function resolveDataClass(string $route): string
    {
        $folder = Str::studly($route);
        /** @var class-string<Data> $class */
        $class = "App\\Data\\{$folder}\\Table";

        if (! class_exists($class)) {
            abort(500, "Data Object [{$class}] no definido.");
        }

        return $class;
    }
}
