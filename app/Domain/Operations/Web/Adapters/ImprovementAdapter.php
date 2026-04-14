<?php

declare(strict_types=1);

namespace App\Domain\Operations\Web\Adapters;

use App\Domain\Operations\Actions\GetImprovementData;
use App\Domain\Operations\Data\ImprovementTableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class ImprovementAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route'  => 'operations.improvement',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title:          'SGC / Improvement',
            subtitle:       'Gestión de acciones correctivas y de mejora',
            icon:           'ri-line-chart-line',
            newButtonLabel: 'New Improvement',
            modalWidth:     '80',
            columns:        SchemaGenerator::toColumns(ImprovementTableData::class),
            formFields:     [],
        );
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $filters = $request->collect('filter')->pluck('value', 'field')->toArray();
        $sorts   = $request->collect('sort')->pluck('dir', 'field')->toArray();

        $result = GetImprovementData::run(
            filters: $filters,
            sorts:   $sorts,
            page:    $request->integer('page', 1),
            size:    $request->integer('size', 15)
        );

        return response()->json([
            'data'      => $result['data'],
            'last_page' => $result['last_page'],
            'last_row'  => $result['total'],
        ]);
    }
}
