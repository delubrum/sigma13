<?php

declare(strict_types=1);

namespace App\Domain\MaintenanceP\Web\Adapters;

use App\Contracts\HasModule;
use App\Domain\MaintenanceP\Actions\GetMaintenancePDataAction;
use App\Domain\MaintenanceP\Data\TableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class IndexAdapter implements HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route'  => 'maintenancep',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title:          'Mantenimiento Preventivo',
            subtitle:       'Gestión de mantenimiento preventivo programado',
            icon:           'ri-calendar-check-line',
            newButtonLabel: null,
            modalWidth:     '80',
            columns:        SchemaGenerator::toColumns(TableData::class),
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

        $result = GetMaintenancePDataAction::run(
            filters: $filters,
            sorts:   $sorts,
            page:    $request->integer('page', 1),
            size:    $request->integer('size', 50),
        );

        return response()->json([
            'data'      => $result->items,
            'last_page' => $result->lastPage,
            'last_row'  => $result->total,
        ]);
    }
}
