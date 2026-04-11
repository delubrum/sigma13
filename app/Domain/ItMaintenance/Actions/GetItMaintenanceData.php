<?php

declare(strict_types=1);

namespace App\Domain\ItMaintenance\Actions;

use App\Domain\ItMaintenance\Data\Table;
use App\Domain\ItMaintenance\Queries\ItTableQuery;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetItMaintenanceData
{
    use AsAction;

    /**
     * @param array<string, mixed> $filters
     * @param array<string, string> $sorts
     * @return array{data: array<int, Table>, total: int, last_page: int}
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $paginator = ItTableQuery::make()
            ->apply($filters, $sorts)
            ->paginate($page, $size);

        return [
            'data'      => $paginator->getCollection()->map(fn($it) => new Table(
                id:          $it->id,
                date:        $it->created_at?->format('Y-m-d') ?? '',
                user:        $it->user?->username ?? 'Unknown',
                facility:    $it->facility,
                asset:       $it->asset ? "{$it->asset->hostname} | {$it->asset->serial} | {$it->asset->sap}" : 'N/A',
                priority:    $it->priority,
                status:      $this->renderStatusBadge($it->status),
                description: $it->description,
                assignee:    $it->assignee?->username ?? 'Unassigned',
                days:        $it->created_at?->diffInDays($it->closed_at ?? now()) ?? 0,
                started_at:  $it->started_at?->format('Y-m-d H:i') ?? '',
                attended:    $it->ended_at?->format('Y-m-d H:i') ?? '',
                closed:      $it->closed_at?->format('Y-m-d H:i') ?? '',
                time:        (float) ($it->time_sum ?? 0),
                sgc:         $it->sgc,
                rating:      $it->rating,
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
            'Attended' => 'border-purple-500 text-purple-500',
            'Closed'   => 'border-green-500 text-green-500',
            'Rated'    => 'border-emerald-500 text-emerald-500',
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
