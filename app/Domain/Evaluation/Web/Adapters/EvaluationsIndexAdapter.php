<?php

declare(strict_types=1);

namespace App\Domain\Evaluation\Web\Adapters;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Evaluation\Actions\GetEvaluationsAction;
use App\Domain\Evaluation\Data\EvaluationsTableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\Data;

final class EvaluationsIndexAdapter implements HasDetail, HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('shared::components.index', [
            'route' => 'evaluation',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Suppliers Evaluation',
            icon: 'ri-survey-line',
            subtitle: 'Overview of supplier performance and compliance',
            newButtonLabel: '',
            columns: SchemaGenerator::toColumns(EvaluationsTableData::class),
        );
    }

    public function sidebarData(int $id): Data
    {
        // En una implementación real aquí llamaríamos a un Action específico
        // Por ahora devolvemos un objeto anónimo compatible para no romper la interfaz
        return new class extends Data {
            public string $title = 'Detalle de Evaluación';
        };
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $result = GetEvaluationsAction::run(
            page: (int) $request->get('page', 1),
            size: (int) $request->get('size', 15),
            filters: $request->get('filter', [])
        );

        return response()->json([
            'data' => $result->items,
            'last_page' => $result->lastPage,
            'last_row' => $result->total,
        ]);
    }
}
