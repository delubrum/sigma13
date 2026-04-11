<?php

declare(strict_types=1);

namespace App\Domain\ItMaintenance\Queries;

use App\Domain\ItMaintenance\Models\It;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class ItTableQuery
{
    /** @var Builder<It> */
    public private(set) Builder $query;

    public function __construct()
    {
        /** @var Builder<It> $query */
        $query = It::query()->with(['user', 'asset', 'assignee'])->withSum('items as time_sum', 'duration');
        $this->query = $query;
    }

    public static function make(): self
    {
        return new self();
    }

    /**
     * @param array<string, mixed> $filters
     * @param array<string, string> $sorts
     */
    public function apply(array $filters, array $sorts): self
    {
        return $this->filter($filters)->sort($sorts);
    }

    /** @param array<string, mixed> $filters */
    private function filter(array $filters): self
    {
        foreach ($filters as $field => $value) {
            if (blank($value)) continue;

            match ((string) $field) {
                'id'       => $this->query->where('it.id', (int) $value),
                'user'     => $this->query->whereHas('user', fn(Builder $q) => $q->where('username', 'ilike', "%$value%")),
                'assignee' => $this->query->whereHas('assignee', fn(Builder $q) => $q->where('username', 'ilike', "%$value%")),
                'asset'    => $this->query->whereHas('asset', fn(Builder $q) => 
                    $q->where('hostname', 'ilike', "%$value%")
                      ->orWhere('serial', 'ilike', "%$value%")
                      ->orWhere('sap', 'ilike', "%$value%")
                ),
                'date'     => $this->applyDateFilter((string) $value, 'created_at'),
                'started'  => $this->applyDateFilter((string) $value, 'started_at'),
                'closed'   => $this->applyDateFilter((string) $value, 'closed_at'),
                default    => $this->query->where("it.$field", 'ilike', "%$value%"),
            };
        }
        return $this;
    }

    /** @param array<string, string> $sorts */
    private function sort(array $sorts): self
    {
        if (empty($sorts)) {
            $this->query->orderByRaw("
                CASE status 
                    WHEN 'Open' THEN 1 
                    WHEN 'Started' THEN 2 
                    WHEN 'Attended' THEN 3 
                    WHEN 'Closed' THEN 4 
                    WHEN 'Rated' THEN 5 
                    WHEN 'Rejected' THEN 6 
                    ELSE 7 
                END, created_at DESC
            ");
            return $this;
        }

        foreach ($sorts as $field => $dir) {
            $this->query->orderBy((string) $field, $dir);
        }
        return $this;
    }

    /** @return LengthAwarePaginator<It> */
    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        return $this->query->paginate($size, ['*'], 'page', $page);
    }

    private function applyDateFilter(string $value, string $column): void
    {
        str_contains($value, ' to ')
            ? $this->query->whereBetween($column, explode(' to ', $value))
            : $this->query->whereDate($column, $value);
    }
}
