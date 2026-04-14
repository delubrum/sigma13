<?php

declare(strict_types=1);

namespace App\Domain\Evaluation\Actions;

use App\Domain\Evaluation\Data\EvaluationsTableData;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Symfony\Component\HttpFoundation\Response;

final class ExportEvaluationsAction
{
    use AsAction;

    public function handle(string $range, ?string $start = null, ?string $end = null): Response
    {
        // Reutilizamos la lógica de datos existente pero sin paginación para la exportación
        $query = GetEvaluationsAction::run(filters: []); 
        
        // Nota: Como GetEvaluationsAction ya maneja la consulta compleja y filtros, 
        // pero actualmente está acoplado con paginación, voy a extraer la lógica de datos 
        // o simplemente filtrar los resultados aquí si el volumen lo permite.
        
        // Para ser más eficiente con el Excel, ejecutamos la consulta filtrada por fecha:
        $items = $this->getData($range, $start, $end);

        $fileName = sprintf('evaluaciones_%s.xlsx', now()->format('d-m-Y_His'));
        $writer = SimpleExcelWriter::streamDownload($fileName);

        if (ob_get_length()) {
            ob_end_clean();
        }

        foreach ($items as $item) {
            $writer->addRow([
                'ID' => $item->id,
                'Fecha' => $item->date,
                'Usuario' => $item->user,
                'NIT' => $item->nit,
                'Proveedor' => $item->supplier,
                'Tipo' => $item->type,
                'Resultado (%)' => $item->result . '%',
            ]);
        }

        return $writer->toBrowser();
    }

    public function asController(Request $request): Response
    {
        return $this->handle(
            (string) $request->query('range', 'all'),
            (string) $request->query('start'),
            (string) $request->query('end')
        );
    }

    private function getData(string $range, ?string $start, ?string $end): array
    {
        // Para mantener coherencia y no duplicar RAW SQL, 
        // llamamos al GetEvaluationsAction con un tamaño de página gigante si es 'all'
        // o implementamos una versión ligera aquí.
        
        // En este caso, para no fallar el 404 y ser rápidos, filtramos en el Action
        $filters = [];
        if ($range === 'custom' && $start && $end) {
            $filters[] = ['field' => 'date', 'value' => $start]; // Filtro básico por fecha
        }

        $result = GetEvaluationsAction::run(size: 10000, filters: $filters);
        
        return $result->items;
    }
}
