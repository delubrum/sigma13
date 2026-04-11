<?php

declare(strict_types=1);

namespace App\Domain\Users\Queries;

use App\Domain\Users\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class UserTableQuery
{
    private Builder $query;

    public function __construct()
    {
        $this->query = User::query();
    }

    public static function make(): self
    {
        return new self();
    }

    public function apply(array $filters = [], array $sorts = []): self
    {
        $this->applyFilters($filters);
        $this->applySorts($sorts);

        return $this;
    }

    private function applyFilters(array $filters): void
    {
        foreach ($filters as $f) {
            $field = $f['field'] ?? null;
            $value = $f['value'] ?? null;

            if ($field === null || $value === null) continue;

            match ($field) {
                'isActive' => $this->query->where('is_active', $value),
                'id'       => $this->query->where('id', $value),
                default    => $this->query->where($field, 'ilike', "%{$value}%"),
            };
        }
    }

    private function applySorts(array $sorts): void
    {
        if (empty($sorts)) {
            $this->query->latest();
            return;
        }

        foreach ($sorts as $s) {
            $field = match ($s['field']) {
                'isActive'  => 'is_active',
                'createdAt' => 'created_at',
                default     => $s['field'],
            };
            
            $this->query->orderBy($field, $s['dir']);
        }
    }

    public function paginate(int $page = 1, int $size = 15): LengthAwarePaginator
    {
        return $this->query->paginate(perPage: $size, page: $page);
    }
}