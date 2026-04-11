<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Web\Actions;

use App\Domain\Tickets\Actions\GetTicketsData;
use App\Domain\Tickets\Data\Table;
use App\Domain\Tickets\Data\UpsertData;
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
            'route' => 'tickets',
            'config' => $this->config(),
        ]);
    }

    public function config(): Config
    {
        return new Config(
            title: 'Admin Desk',
            subtitle: 'Gestión de tickets y requerimientos administrativos',
            icon: 'ri-ticket-2-line',
            newButtonLabel: 'Nuevo Ticket',
            modalWidth: '60',
            columns: SchemaGenerator::toColumns(Table::class),
            formFields: SchemaGenerator::toFields(UpsertData::class),
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

        $result = GetTicketsData::run(
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
