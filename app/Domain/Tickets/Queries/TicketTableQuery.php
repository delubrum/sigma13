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
                'id' => $this->query->where('tickets.id', is_numeric($value) ? (int) $value : 0),
                'user' => $this->query->whereHas('user', fn (Builder $q) => $q->where('name', 'ilike', '%'.$valStr.'%')),
                'date' => $this->applyDateFilter($valStr, 'created_at'),
                'started' => $this->applyDateFilter($valStr, 'started_at'),
                'closed' => $this->applyDateFilter($valStr, 'closed_at'),
                'type' => $this->query->where('kind', 'ilike', '%'.$valStr.'%'),
                default => $this->query->where('tickets.'.$field, 'ilike', '%'.$valStr.'%'),
            };
        }

        return $this;
    }

    /** @param array<string, string> $sorts */
    private function sort(array $sorts): self
    {
        if ($sorts === []) {
            $this->query->latest();

            return $this;
        }

        foreach ($sorts as $field => $dir) {
            $this->query->orderBy((string) $field, $dir);
        }

        return $this;
    }

    /** @return LengthAwarePaginator<int, Ticket> */
    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, Ticket> $paginator */
        $paginator = $this->query->paginate($size, ['*'], 'page', $page);

        return $paginator;
    }

    private function applyDateFilter(mixed $value, string $column): void
    {
        $valStr = (string) (is_scalar($value) ? $value : '');
        if (str_contains($valStr, ' to ')) {
            $this->query->whereBetween($column, explode(' to ', $valStr));
        } else {
            $this->query->whereDate($column, $valStr);
        }
    }
}
