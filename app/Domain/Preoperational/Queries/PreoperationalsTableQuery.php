<?php

declare(strict_types=1);

namespace App\Domain\Preoperational\Queries;

use App\Domain\Assets\Models\Asset;
use App\Domain\Preoperational\Models\Preoperational;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class PreoperationalsTableQuery
{
    /** @var Builder<Preoperational> */
    public private(set) Builder $query;

    public function __construct()
    {
        /** @var Builder<Preoperational> $query */
        $query = Preoperational::query()->with([
            'vehicle',
            'user',
        ])->where('status', '<>', 'draft');
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
                'user' => $this->query->whereHas('user', fn (Builder $q) => $q->where('username', 'ilike', '%'.$valStr.'%')),
                'vehicle' => $this->query->whereHas('vehicle', fn (Builder $q) => $q->where('hostname', 'ilike', '%'.$valStr.'%')->orWhere('serial', 'ilike', '%'.$valStr.'%')->orWhere('sap', 'ilike', '%'.$valStr.'%')),
                'date' => $this->applyDateFilter($value),
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
                'user' => $this->query->orderBy(
                    User::query()
                        ->select('username')
                        ->whereColumn('id', 'preoperational.user_id')
                        ->limit(1),
                    $dir
                ),
                'vehicle' => $this->query->orderBy(
                    Asset::query()
                        ->select('hostname')
                        ->whereColumn('id', 'preoperational.vehicle_id')
                        ->limit(1),
                    $dir
                ),
                'date' => $this->query->orderBy('created_at', $dir),
                default => $this->query->orderBy((string) $field, $dir),
            };
        }

        return $this;
    }

    /** @return LengthAwarePaginator<int, Preoperational> */
    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, Preoperational> $paginator */
        $paginator = $this->query->paginate($size, ['*'], 'page', $page);

        return $paginator;
    }

    private function applyDateFilter(mixed $value): void
    {
        $valStr = is_scalar($value) ? (string) $value : '';
        if (str_contains($valStr, ' to ')) {
            $this->query->whereBetween('created_at', explode(' to ', $valStr));
        } else {
            $this->query->whereDate('created_at', $valStr);
        }
    }
}
