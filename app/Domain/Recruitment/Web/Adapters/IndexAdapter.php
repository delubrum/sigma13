<?php

declare(strict_types=1);

namespace App\Domain\Recruitment\Web\Adapters;

use App\Contracts\HasModule;
use App\Domain\Recruitment\Actions\GetRecruitmentDataAction;
use App\Domain\Recruitment\Data\TableData;
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
            'route'  => 'recruitment',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title:          'Reclutamiento',
            subtitle:       'Gestión de procesos de selección y vacantes',
            icon:           'ri-user-search-line',
            newButtonLabel: 'Nuevo Proceso',
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

        $result = GetRecruitmentDataAction::run(
            filters: $filters,
            sorts:   $sorts,
            page:    $request->integer('page', 1),
            size:    $request->integer('size', 15),
        );

        return response()->json([
            'data'      => $result->items,
            'last_page' => $result->lastPage,
            'last_row'  => $result->total,
        ]);
    }
}
