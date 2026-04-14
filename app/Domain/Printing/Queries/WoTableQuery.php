<?php

declare(strict_types=1);

namespace App\Domain\Printing\Queries;

use App\Domain\Printing\Models\Wo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class WoTableQuery
{
    /** @var Builder<Wo> */
    public private(set) Builder $query;

    public function __construct()
    {
        /** @var Builder<Wo> $q */
        $q = Wo::query()
            ->select('wo.*', DB::raw("(SELECT username FROM users WHERE id = wo.user_id LIMIT 1) as username"));
        $this->query = $q;
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
            $val = (string) (is_scalar($value) ? $value : '');

            match ((string) $field) {
                'id'      => $this->query->where('code', 'ilike', "%{$val}%"),
                'project' => $this->query->where('project', 'ilike', "%{$val}%"),
                'es'      => $this->query->where('es_id', 'ilike', "%{$val}%"),
                'user'    => $this->query->whereRaw("EXISTS (SELECT 1 FROM users WHERE id = wo.user_id AND username ILIKE ?)", ["%{$val}%"]),
                'date'    => $this->applyDateFilter($value),
                default   => null,
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
                'user' => $this->query->orderBy(
                    DB::table('users')->select('username')
                        ->whereColumn('id', 'wo.user_id')->limit(1),
                    $dir
                ),
                default => $this->query->orderBy((string) $field, $dir),
            };
        }

        return $this;
    }

    /** @return LengthAwarePaginator<int, Wo> */
    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, Wo> $paginator */
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
