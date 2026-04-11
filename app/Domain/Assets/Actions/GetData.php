<?php

declare(strict_types=1);

namespace App\Domain\Assets\Actions;

use App\Domain\Assets\Data\Table;
use App\Domain\Assets\Models\Asset;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

final class GetData
{
    use AsAction;

    /**
     * Core logic to fetch and filter assets for Tabulator.
     * Fully decoupled from HTTP.
     * 
     * @param array{sort?: array<int, array{field: string, dir: string}>, filter?: array<int, array{field: string, value: mixed}>, size?: int, page?: int} $options
     * @return array{data: \Illuminate\Support\Collection<int, Table>, last_page: int, total: int}
     */
    public function handle(array $options = []): array
    {
        $size = max(1, $options['size'] ?? 15);
        $page = max(1, $options['page'] ?? 1);

        $query = Asset::query()->with(['currentAssignment.employee', 'category', 'brand', 'model']);

        $filters = $options['filter'] ?? [];
        $this->applyFilters($query, $filters);

        $sorts = $options['sort'] ?? [['field' => 'id', 'dir' => 'desc']];
        $sort = $sorts[0] ?? ['field' => 'id', 'dir' => 'desc'];
        $field = (string) ($sort['field'] ?? 'id');
        $dir = strtolower((string) ($sort['dir'] ?? 'desc')) === 'asc' ? 'asc' : 'desc';
        $table = $query->getModel()->getTable();

        match ($field) {
            'criticality' => $query->orderByCriticality($dir),
            'assignee' => $query->orderByAssignee($dir),
            'work_mode' => $query->orderBy("$table.work_mode", $dir),
            'date' => $query->orderBy("$table.acquisition_date", $dir),
            default => $query->orderBy("$table.$field", $dir),
        };

        $paginator = $query->paginate($size, ['*'], 'page', $page);

        return [
            'data' => $paginator->getCollection()->map(fn (Asset $asset) => Table::fromModel($asset))->values(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<Asset>  $query
     * @param  array<int, array{field: string, value: mixed}>  $filters
     */
    private function applyFilters(\Illuminate\Database\Eloquent\Builder $query, array $filters): void
    {
        foreach ($filters as $f) {
            $field = $f['field'] ?? null;
            $value = $f['value'] ?? null;

            if (trim((string) ($value ?? '')) === '') {
                continue;
            }

            if (! is_scalar($value)) {
                continue;
            }

            $valStr = (string) $value;

            match ($field) {
                'id' => $query->where('id', (int) $value),
                'status' => $query->where('status', $valStr),
                'work_mode' => $query->where('work_mode', 'ilike', "%{$valStr}%"),
                'confidentiality', 'integrity', 'availability' => $query->where($field, (int) $value),
                'criticality' => $query->whereRaw('(confidentiality + integrity + availability) = ?', [(int) $value]),
                'date', 'acquisition_date' => str_contains($valStr, ' to ')
                    ? $query->whereBetween('acquisition_date', explode(' to ', $valStr))
                    : $query->whereDate('acquisition_date', $valStr),
                'assignee' => $query->whereHas('currentAssignment.employee', fn (\Illuminate\Database\Eloquent\Builder $q) => $q->where('name', 'ilike', "%{$valStr}%")),
                default => $query->where($field, 'ilike', "%{$valStr}%"),
            };
        }
    }
}
