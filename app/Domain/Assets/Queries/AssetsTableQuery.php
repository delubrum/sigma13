<?php

declare(strict_types=1);

namespace App\Domain\Assets\Queries;

use App\Domain\Assets\Models\Asset;
use App\Domain\Assets\Models\AssetEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class AssetsTableQuery
{
    /** @var Builder<Asset> */
    public private(set) Builder $query;

    public function __construct()
    {
        /** @var Builder<Asset> $query */
        $query = Asset::query()->with([
            'currentAssignment.employee',
        ]);
        $this->query = $query;
    }

    public static function make(): self
    {
        return new self;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, string>  $sorts
     */
    public function apply(array $filters, array $sorts): self
    {
        return $this->filter($filters)->sort($sorts);
    }

    /** @param array<string, mixed> $filters */
    private function filter(array $filters): self
    {
        foreach ($filters as $field => $value) {
            if (blank($value)) {
                continue;
            }

            $valStr = (string) (is_scalar($value) ? $value : '');

            match ((string) $field) {
                'id' => $this->query->where('id', (int) (is_scalar($value) ? $value : 0)),
                'assignee' => $this->query->whereHas('currentAssignment.employee', fn (Builder $q) => $q->where('name', 'ilike', '%'.$valStr.'%')),
                'criticality' => $this->query->whereRaw('(confidentiality + integrity + availability) = ?', [(int) (is_scalar($value) ? $value : 0)]),
                'date', 'acquisition_date' => $this->applyDateFilter($value),
                default => $this->query->where((string) $field, 'ilike', '%'.$valStr.'%'),
            };
        }

        return $this;
    }

    /** @param array<string, string> $sorts */
    private function sort(array $sorts): self
    {
        if ($sorts === []) {
            $this->query->orderByDesc('id');

            return $this;
        }

        foreach ($sorts as $field => $dir) {
            match ((string) $field) {
                'assignee' => $this->query->orderBy(
                    AssetEvent::query()
                        ->select('employee_id')
                        ->whereColumn('asset_id', 'assets.id')
                        ->where('kind', 'assignment')
                        ->latest('id')
                        ->limit(1)
                        ->toBase(),
                    $dir
                ),
                'criticality' => $this->query->orderByRaw(
                    '(confidentiality + integrity + availability) '.($dir === 'asc' ? 'ASC' : 'DESC')
                ),
                default => $this->query->orderBy((string) $field, $dir),
            };
        }

        return $this;
    }

    /** @return LengthAwarePaginator<int, Asset> */
    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, Asset> $paginator */
        $paginator = $this->query->paginate($size, ['*'], 'page', $page);

        return $paginator;
    }

    private function applyDateFilter(mixed $value): void
    {
        $valStr = is_scalar($value) ? (string) $value : '';
        if (str_contains($valStr, ' to ')) {
            $this->query->whereBetween('acquisition_date', explode(' to ', $valStr));
        } else {
            $this->query->whereDate('acquisition_date', $valStr);
        }
    }
}
