<?php

declare(strict_types=1);

namespace App\Domain\MaintenanceP\Actions;

use App\Domain\MaintenanceP\Data\Table;
use App\Domain\MaintenanceP\Queries\MntPreventiveTableQuery;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetMaintenancePData
{
    use AsAction;

    /**
     * @param array<string, mixed> $filters
     * @param array<string, string> $sorts
     * @return array{data: array<int, Table>, total: int, last_page: int}
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $paginator = MntPreventiveTableQuery::make()
            ->apply($filters, $sorts)
            ->paginate($page, $size);

        return [
            'data'      => $paginator->getCollection()->map(fn($m) => new Table(
                id:        $m->id,
                start:     $m->scheduled_start?->format('Y-m-d') ?? '',
                end:       $m->scheduled_end?->format('Y-m-d') ?? '',
                asset:     $m->asset ? "{$m->asset->hostname} | {$m->asset->serial} | {$m->asset->sap}" : 'N/A',
                status:    $this->renderStatusBadge($m->status),
                days:      $this->renderDaysBadge($m->scheduled_end, $m->closed_at),
                activity:  $m->activity ?: ($m->form?->activity ?? ''),
                frequency: $m->form?->frequency ?? '',
                started:   $m->started?->format('Y-m-d H:i') ?? '',
                attended:  $m->attended?->format('Y-m-d H:i') ?? '',
                closed:    $m->closed_at?->format('Y-m-d H:i') ?? '',
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
            default    => 'border-sigma-b text-sigma-tx2',
        };

        return sprintf(
            '<span class="px-2 py-0.5 rounded border %s font-bold uppercase text-[10px]">%s</span>',
            $color,
            $status ?? 'Open'
        );
    }

    private function renderDaysBadge(?\DateTimeInterface $end, ?\DateTimeInterface $closed): string
    {
        if (!$end) return '—';
        
        $target = $closed ?? now();
        $days = (int) now()->diffInDays($end, false);
        
        $color = ($days >= 0) ? 'text-green-500' : 'text-red-500';
        if ($closed) {
            $color = 'text-gray-500';
        }

        return sprintf('<span class="font-bold %s">%d</span>', $color, $days);
    }
}
