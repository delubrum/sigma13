<?php

declare(strict_types=1);

namespace App\Domain\Performance\Web\Adapters;

use App\Domain\Performance\Actions\GetPerformanceData;
use App\Domain\Performance\Data\PerformanceTableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class PerformanceAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route' => 'performance',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Performance Evaluation',
            icon: 'ri-flashlight-line',
            subtitle: 'Comprehensive employee performance tracking',
            newButtonLabel: 'New Evaluation',
            modalWidth: '80',
            columns: SchemaGenerator::toColumns(PerformanceTableData::class),
        );
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $filters = $request->collect('filter')->pluck('value', 'field')->toArray();
        $sorts = $request->collect('sort')->pluck('dir', 'field')->toArray();

        /** @var array{data: array<int, mixed>, total: int, last_page: int} $result */
        $result = GetPerformanceData::run(
            filters: $filters,
            sorts: $sorts,
            page: $request->integer('page', 1),
            size: $request->integer('size', 15)
        );

        return response()->json([
            'data' => $result['data'],
            'last_page' => $result['last_page'],
            'total' => $result['total'],
        ]);
    }
}
