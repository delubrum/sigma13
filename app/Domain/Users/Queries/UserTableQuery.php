<?php

declare(strict_types=1);

namespace App\Domain\Users\Queries;

use App\Domain\Users\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final readonly class UserTableQuery
{
    /** @var Builder<User> */
    private Builder $query;

    public function __construct()
    {
        $this->query = User::query();
    }

    public static function make(): self
    {
        return new self;
    }

    /**
     * @param  array<int, array{field: string, value: mixed}>  $filters
     * @param  array<int, array{field: string, dir: string}>  $sorts
     */
    public function apply(array $filters = [], array $sorts = []): self
    {
        $this->applyFilters($filters);
        $this->applySorts($sorts);

        return $this;
    }

    /**
     * @param  array<int, array{field: string, value: mixed}>  $filters
     */
    private function applyFilters(array $filters): void
    {
        foreach ($filters as $f) {
            $field = $f['field'] ?? null;
            $value = $f['value'] ?? null;
            if ($field === null) {
                continue;
            }
            if ($value === null) {
                continue;
            }

            $strValue = is_scalar($value) ? (string) $value : '';

            match ($field) {
                'isActive' => $this->query->where('is_active', $value),
                'id' => $this->query->where('id', $value),
                default => $this->query->where((string) $field, 'ilike', '%'.$strValue.'%'),
            };
        }
    }

    /**
     * @param  array<int, array{field: string, dir: string}>  $sorts
     */
    private function applySorts(array $sorts): void
    {
        if ($sorts === []) {
            $this->query->latest();

            return;
        }

        foreach ($sorts as $s) {
            $field = match ($s['field']) {
                'isActive' => 'is_active',
                'createdAt' => 'created_at',
                default => $s['field'],
            };

            $this->query->orderBy($field, $s['dir']);
        }
    }

    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(int $page = 1, int $size = 15): LengthAwarePaginator
    {
        return $this->query->paginate(perPage: $size, page: $page);
    }
}
