<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Web\Adapters;

use App\Contracts\HasDetail;
use App\Contracts\HasModule;
use App\Domain\Shared\Data\ActionOption;
use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Data\Tabs;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Domain\Tickets\Actions\GetTicketsDataAction;
use App\Domain\Tickets\Actions\GetTicketSidebarAction;
use App\Domain\Tickets\Data\SidebarData;
use App\Domain\Tickets\Data\TableData;
use App\Domain\Tickets\Data\UpsertData;
use App\Support\HtmxOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lorisleiva\Actions\Concerns\AsAction;

final class IndexAdapter implements HasDetail, HasModule
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
            icon: 'ri-ticket-2-line',
            subtitle: 'Gestión de tickets y requerimientos',
            newButtonLabel: 'Nuevo Ticket',
            modalWidth: '90',
            columns: SchemaGenerator::toColumns(TableData::class),
            formFields: SchemaGenerator::toFields(UpsertData::class),
            tabs: [
                new Tabs(key: 'tasks', label: 'Actividad', icon: 'ri-history-line', route: 'tickets.tasks', default: true),
            ],
            options: [
                new ActionOption(
                    label: 'Editar Ticket',
                    icon: 'ri-edit-line',
                    route: 'tickets/edit',
                    target: '#modal-body',
                    level: 1
                ),
                new ActionOption(
                    label: 'Cerrar Ticket',
                    icon: 'ri-check-double-line',
                    route: 'tickets/close',
                    target: '#modal-body',
                    level: 2
                ),
                new ActionOption(
                    label: 'Rechazar Ticket',
                    icon: 'ri-close-circle-line',
                    route: 'tickets/reject',
                    target: '#modal-body-2',
                    level: 2
                ),
            ],
        );
    }

    public function sidebarData(int $id): SidebarData
    {
        /** @var SidebarData $result */
        $result = GetTicketSidebarAction::run($id);

        return $result;
    }

    public function asController(): Response
    {
        return $this->handle();
    }

    public function asData(Request $request): JsonResponse
    {
        $filters = $request->collect('filter')->pluck('value', 'field')->toArray();
        $sorts = $request->collect('sort')->pluck('dir', 'field')->toArray();

        /** @var PaginatedResult<TableData> $result */
        $result = GetTicketsDataAction::run(
            filters: $filters,
            sorts: $sorts,
            page: $request->integer('page', 1),
            size: $request->integer('size', 15),
        );

        return response()->json([
            'data' => $result->items,
            'last_page' => $result->lastPage,
            'last_row' => $result->total,
        ]);
    }
}
