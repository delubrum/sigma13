<?php

declare(strict_types=1);

namespace App\Domain\Employees\Actions;

use App\Domain\Employees\Data\Table;
use App\Domain\Employees\Queries\EmployeeTableQuery;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetEmployeesData
{
    use AsAction;

    /**
     * @param array<string, mixed> $filters
     * @param array<string, string> $sorts
     * @return array{data: array<int, Table>, total: int, last_page: int}
     */
    public function handle(array $filters = [], array $sorts = [], int $page = 1, int $size = 15): array
    {
        $paginator = EmployeeTableQuery::make()
            ->apply($filters, $sorts)
            ->paginate($page, $size);

        return [
            'data'      => $paginator->getCollection()->map(fn($e) => new Table(
                id:          $e->id,
                name:        $e->name,
                division:    $e->division_name ?? 'N/A', // Need to fix joins if I want these names
                profile:     $e->profile_name ?? 'N/A',
                city:        $e->city,
                start_date:  $e->start_date?->format('Y-m-d') ?? '',
                status:      $this->renderStatusBadge($e->status),
                updated_at:  $e->latest_update ? \Carbon\Carbon::parse($e->latest_update)->format('Y-m-d H:i') : 'N/A',
            ))->toArray(),
            'total'     => $paginator->total(),
            'last_page' => $paginator->lastPage(),
        ];
    }

    private function renderStatusBadge(mixed $status): string
    {
        $status = (string) $status;
        $color = ($status === '1' || $status === 'true') ? 'border-green-500 text-green-500' : 'border-red-500 text-red-500';
        $label = ($status === '1' || $status === 'true') ? 'Activo' : 'Inactivo';

        return sprintf(
            '<span class="px-2 py-0.5 rounded border %s font-bold uppercase text-[10px]">%s</span>',
            $color,
            $label
        );
    }
}
