<?php

declare(strict_types=1);

namespace App\Domain\Employees\Web\Actions;

use App\Domain\Employees\Actions\GetEmployeesData;
use App\Domain\Employees\Data\Table;
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
            'route' => 'employees',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Employees',
            subtitle: 'Directorio y gestión de personal',
            icon: 'ri-team-line',
            newButtonLabel: 'Nuevo Empleado',
            modalWidth: '60',
            columns: SchemaGenerator::toColumns(Table::class),
            formFields: [],
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

        $result = GetEmployeesData::run(
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
