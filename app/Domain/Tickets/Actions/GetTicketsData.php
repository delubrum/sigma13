<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Actions;

use App\Domain\Tickets\Data\Table;
use App\Domain\Tickets\Queries\TicketTableQuery;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetTicketsData
{
    use AsAction;

    /**
     * @param array<string, mixed> $filters
     * @param array<string, string> $sorts
     * @return array{data: array<int, Table>, total: int, last_page: int}
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $paginator = TicketTableQuery::make()
            ->apply($filters, $sorts)
            ->paginate($page, $size);

        return [
            'data'      => $paginator->getCollection()->map(fn($t) => new Table(
                id:          $t->id,
                type:        $t->kind,
                date:        $t->created_at?->format('Y-m-d') ?? '',
                user:        $t->user?->username ?? 'Unknown',
                facility:    $t->facility,
                priority:    $t->priority,
                description: $t->description,
                days:        $t->created_at?->diffInDays($t->closed_at ?? now()) ?? 0,
                started:     $t->started_at?->format('Y-m-d H:i') ?? '',
                closed:      $t->closed_at?->format('Y-m-d H:i') ?? '',
                status:      $this->renderStatusBadge($t->status),
            ))->toArray(),
            'total'     => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    private function renderStatusBadge(?string $status): string
    {
        $color = match ($status) {
            'Open'     => 'border-blue-500 text-blue-500',
            'Started'  => 'border-yellow-500 text-yellow-500',
            'Closed'   => 'border-green-500 text-green-500',
            'Rejected' => 'border-red-500 text-red-500',
            default    => 'border-sigma-b text-sigma-tx2',
        };

        return sprintf(
            '<span class="px-2 py-0.5 rounded border %s font-bold uppercase text-[10px]">%s</span>',
            $color,
            $status ?? 'Open'
        );
    }
}
