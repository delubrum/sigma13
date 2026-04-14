<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Web\Adapters;

use App\Domain\Shared\Data\Config;
use App\Domain\Shared\Data\PaginatedResult;
use App\Domain\Shared\Services\SchemaGenerator;
use App\Domain\Shared\Web\Actions\SubTableAdapter;
use App\Domain\Tickets\Data\ItemUpsertData;
use App\Domain\Tickets\Data\TaskTableData;
use App\Domain\Tickets\Models\TicketItem;
use Illuminate\Support\Carbon;

/** @extends SubTableAdapter<TaskTableData> */
final class TasksAdapter extends SubTableAdapter
{
    #[\Override]
    protected function tabConfig(): Config
    {
        return new Config(
            title: 'Historial de Actividad',
            icon: 'ri-history-line',
            subtitle: 'Registro cronológico de intervenciones y avances',
            newButtonLabel: 'Agregar Avance',
            modalWidth: '40',
            columns: SchemaGenerator::toColumns(TaskTableData::class),
            formFields: SchemaGenerator::toFields(ItemUpsertData::class),
        );
    }

    #[\Override]
    protected function tabRoute(): string
    {
        return 'tickets.tasks';
    }

    #[\Override]
    protected function tabData(int $parentId, int $page, int $size): PaginatedResult
    {
        $paginator = TicketItem::query()
            ->with('user')
            ->where('ticket_id', $parentId)
            ->orderBy('date', 'desc')
            ->paginate($size, ['*'], 'page', $page);

        $items = $paginator->getCollection()->map(function (TicketItem $item): TaskTableData {
            $date = $item->date;
            $userName = $item->user?->name;

            return new TaskTableData(
                date: $date instanceof Carbon ? $date->format('Y-m-d H:i') : '-',
                user: $userName ?? 'Sistema',
                attends: (string) $item->attends,
                notes: (string) $item->notes,
                file: null, // TODO: Implement evidence links if needed
            );
        });

        return new PaginatedResult(
            items: array_values($items->all()),
            lastPage: $paginator->lastPage(),
            total: $paginator->total(),
        );
    }
}
