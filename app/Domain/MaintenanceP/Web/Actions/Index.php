<?php

declare(strict_types=1);

namespace App\Domain\MaintenanceP\Web\Actions;

use App\Domain\MaintenanceP\Actions\GetMaintenancePData;
use App\Domain\MaintenanceP\Data\Table;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class Index
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route' => 'maintenance-p',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Mantenimiento Preventivo',
            subtitle: 'Programación y ejecución de mantenimientos preventivos maquinaria',
            icon: 'ri-calendar-check-line',
            newButtonLabel: 'Programar Nuevo',
            modalWidth: '60',
            columns: SchemaGenerator::toColumns(Table::class),
            formFields: [], // No UpsertData defined yet
            tabs: [],
            options: []
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

        $result = GetMaintenancePData::run(
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
