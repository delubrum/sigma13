<?php

declare(strict_types=1);

namespace App\Domain\Engineering\Web\Adapters;

use App\Contracts\HasModule;
use App\Domain\Engineering\Actions\GetFastenersData;
use App\Domain\Engineering\Data\FastenersTableData;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class FastenersAdapter implements HasModule
{
    use AsAction;
    use HtmxOrchestrator;

    public function handle(): Response
    {
        return $this->hxView('components::index', [
            'route'  => 'engineering/fasteners',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title:          'Fasteners Database',
            subtitle:       'Especificaciones técnicas de tornillería',
            icon:           'ri-settings-line',
            newButtonLabel: 'Nuevo Fastener',
            modalWidth:     '85',
            columns:        SchemaGenerator::toColumns(FastenersTableData::class),
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

        $result = GetFastenersData::run(
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
