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

final class TasksAdapter extends SubTableAdapter
{
    protected function tabConfig(): Config
    {
        return new Config(
            title: 'Historial de Actividad',
            subtitle: 'Registro cronológico de intervenciones y avances',
            icon: 'ri-history-line',
            newButtonLabel: 'Agregar Avance',
            modalWidth: '40',
            columns: SchemaGenerator::toColumns(TaskTableData::class),
            formFields: SchemaGenerator::toFields(ItemUpsertData::class),
        );
    }

    protected function tabRoute(): string
    {
        return 'tickets.tasks';
    }

    protected function tabData(int $parentId, int $page, int $size): PaginatedResult
    {
        $paginator = TicketItem::query()
            ->with('user')
            ->where('ticket_id', $parentId)
            ->orderBy('date', 'desc')
            ->paginate($size, ['*'], 'page', $page);

        $items = $paginator->getCollection()->map(fn ($item) => new TaskTableData(
            date:    $item->date?->format('Y-m-d H:i') ?? '-',
            user:    $item->user?->name ?? 'Sistema',
            attends: $item->attends ?? '-',
            notes:   $item->notes ?? '',
            file:    null, // TODO: Implement evidence links if needed
        ));

        return new PaginatedResult(
            items:    $items->all(),
            lastPage: $paginator->lastPage(),
            total:    $paginator->total(),
        );
    }
}
