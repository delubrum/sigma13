<?php

declare(strict_types=1);

namespace App\Domain\Operations\Web\Adapters;

use App\Domain\Operations\Actions\GetPrintData;
use App\Domain\Operations\Data\PrintTableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class PrintAdapter
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route'  => 'operations.print',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title:          'Operations / Print WO',
            subtitle:       'Gestión de impresión de órdenes de trabajo y etiquetas',
            icon:           'ri-printer-line',
            newButtonLabel: 'New Label',
            modalWidth:     '60',
            columns:        SchemaGenerator::toColumns(PrintTableData::class),
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

        $result = GetPrintData::run(
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
