<?php

declare(strict_types=1);

namespace App\Domain\Maintenance\Web\Adapters;

use App\Domain\Maintenance\Data\PreoperationalTableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class PreoperationalAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route'  => 'maintenance.preoperational',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title:          'Infrastructure / Preoperational',
            subtitle:       'Inspecciones preoperacionales de flota',
            icon:           'ri-steering-line',
            newButtonLabel: 'New Preoperational',
            modalWidth:     '60',
            columns:        SchemaGenerator::toColumns(PreoperationalTableData::class),
            formFields:     [],
        );
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        return response()->json([
            'data'      => [],
            'last_page' => 1,
            'last_row'  => 0,
        ]);
    }
}
