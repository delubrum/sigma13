<?php

declare(strict_types=1);

namespace App\Domain\Tickets\Queries;

use App\Domain\Tickets\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class TicketTableQuery
{
    /** @var Builder<Ticket> */
    public private(set) Builder $query;

    public function __construct()
    {
        /** @var Builder<Ticket> $query */
        $query = Ticket::query()->with(['user', 'assignee']);
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
                'id'       => $this->query->where('tickets.id', (int) $value),
                'user'     => $this->query->whereHas('user', fn(Builder $q) => $q->where('username', 'ilike', "%$value%")),
                'date'     => $this->applyDateFilter((string) $value, 'created_at'),
                'started'  => $this->applyDateFilter((string) $value, 'started_at'),
                'closed'   => $this->applyDateFilter((string) $value, 'closed_at'),
                'type'     => $this->query->where('kind', 'ilike', "%$value%"),
                default    => $this->query->where("tickets.$field", 'ilike', "%$value%"),
            };
        }
        return $this;
    }

    /** @param array<string, string> $sorts */
    private function sort(array $sorts): self
    {
        if (empty($sorts)) {
            $this->query->orderByDesc('created_at');
            return $this;
        }

        foreach ($sorts as $field => $dir) {
            $this->query->orderBy((string) $field, $dir);
        }
        return $this;
    }

    /** @return LengthAwarePaginator<Ticket> */
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
