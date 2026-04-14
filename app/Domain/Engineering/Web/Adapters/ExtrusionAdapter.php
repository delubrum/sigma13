<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Web\Adapters;

use App\Contracts\HasModule;
use App\Domain\Engineering\Actions\GetLibraryData;
use App\Domain\Engineering\Data\LibraryTableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class ExtrusionAdapter implements HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route'  => 'engineering/extrusion',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title:          'Extrusion Dies',
            subtitle:       'Base de datos de perfiles de extrusión',
            icon:           'ri-layout-grid-line',
            newButtonLabel: 'Nuevo Troquel',
            modalWidth:     '85',
            columns:        SchemaGenerator::toColumns(LibraryTableData::class),
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

        $result = GetLibraryData::run(
            filters: $filters,
            sorts:   $sorts,
            page:    $request->integer('page', 1),
            size:    $request->integer('size', 15),
        );

        return response()->json([
            'data'      => $result['data'],
            'last_page' => $result['last_page'],
            'last_row'  => $result['total'],
        ]);
    }
}
