<?php

declare(strict_types=1);

namespace App\Domain\Improvement\Queries;

use App\Domain\Improvement\Models\Improvement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class ImprovementTableQuery
{
    /** @var Builder<Improvement> */
    public private(set) Builder $query;

    public function __construct()
    {
        /** @var Builder<Improvement> $q */
        $q = Improvement::query()->select(
            'improvements.*',
            DB::raw("(SELECT username FROM users WHERE id = improvements.user_id LIMIT 1) as creator_name"),
            DB::raw("(SELECT username FROM users WHERE id = improvements.responsible_id LIMIT 1) as responsible_name"),
        );
        $this->query = $q;
    }

    public static function make(): self
    {
        return new self;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  array<string, string> $sorts
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
            $val = (string) (is_scalar($value) ? $value : '');

            match ((string) $field) {
                'id'          => $this->query->where('code', 'ilike', "%{$val}%"),
                'status'      => $this->query->where('status', 'ilike', "%{$val}%"),
                'process'     => $this->query->where('process', 'ilike', "%{$val}%"),
                'type'        => $this->query->where('type', 'ilike', "%{$val}%"),
                'source'      => $this->query->where('source', 'ilike', "%{$val}%"),
                'creator'     => $this->query->whereRaw(
                    "EXISTS (SELECT 1 FROM users WHERE id = improvements.user_id AND username ILIKE ?)",
                    ["%{$val}%"]
                ),
                'responsible' => $this->query->whereRaw(
                    "EXISTS (SELECT 1 FROM users WHERE id = improvements.responsible_id AND username ILIKE ?)",
                    ["%{$val}%"]
                ),
                'date'        => $this->applyDateFilter($value),
                default       => null,
            };
        }

        return $this;
    }

    /** @param array<string, string> $sorts */
    private function sort(array $sorts): self
    {
        if ($sorts === []) {
            $this->query->orderByDesc('improvements.id');

            return $this;
        }

        foreach ($sorts as $field => $dir) {
            match ((string) $field) {
                'creator' => $this->query->orderBy(
                    DB::table('users')->select('username')->whereColumn('id', 'improvements.user_id')->limit(1),
                    $dir
                ),
                'responsible' => $this->query->orderBy(
                    DB::table('users')->select('username')->whereColumn('id', 'improvements.responsible_id')->limit(1),
                    $dir
                ),
                default => $this->query->orderBy((string) $field, $dir),
            };
        }

        return $this;
    }

    /** @return LengthAwarePaginator<int, Improvement> */
    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, Improvement> $paginator */
        $paginator = $this->query->paginate($size, ['*'], 'page', $page);

        return $paginator;
    }

    private function applyDateFilter(mixed $value): void
    {
        $val = is_scalar($value) ? (string) $value : '';
        if (str_contains($val, ' to ')) {
            $this->query->whereBetween('created_at', explode(' to ', $val));
        } else {
            $this->query->whereDate('created_at', $val);
        }
    }
}
