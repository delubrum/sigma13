<?php

declare(strict_types=1);

namespace App\Domain\MaintenanceP\Queries;

use App\Domain\MaintenanceP\Models\MntPreventive;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class MntPreventiveTableQuery
{
    /** @var Builder<MntPreventive> */
    public private(set) Builder $query;

    public function __construct()
    {
        /** @var Builder<MntPreventive> $query */
        $query = MntPreventive::query()->with(['asset', 'form']);
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
                'id'        => $this->query->where('mnt_preventive.id', (int) $value),
                'asset'     => $this->query->whereHas('asset', fn(Builder $q) => 
                    $q->where('hostname', 'ilike', "%$value%")
                      ->orWhere('serial', 'ilike', "%$value%")
                      ->orWhere('sap', 'ilike', "%$value%")
                ),
                'activity'  => $this->query->where(fn($q) => 
                    $q->where('mnt_preventive.activity', 'ilike', "%$value%")
                      ->orWhereHas('form', fn($f) => $f->where('activity', 'ilike', "%$value%"))
                ),
                'frequency' => $this->query->whereHas('form', fn($q) => $q->where('frequency', 'ilike', "%$value%")),
                'start'     => $this->applyDateFilter((string) $value, 'scheduled_start'),
                'end'       => $this->applyDateFilter((string) $value, 'scheduled_end'),
                'started'   => $this->applyDateFilter((string) $value, 'started'),
                'closed'    => $this->applyDateFilter((string) $value, 'closed_at'),
                default     => $this->query->where("mnt_preventive.$field", 'ilike', "%$value%"),
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
                    ELSE 5 
                END, scheduled_end ASC
            ");
            return $this;
        }

        foreach ($sorts as $field => $dir) {
            $this->query->orderBy((string) $field, $dir);
        }
        return $this;
    }

    /** @return LengthAwarePaginator<MntPreventive> */
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
