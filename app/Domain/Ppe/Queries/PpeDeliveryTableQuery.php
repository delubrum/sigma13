<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Queries;

use App\Domain\Ppe\Models\PpeDelivery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class PpeDeliveryTableQuery
{
    /** @var Builder<PpeDelivery> */
    public private(set) Builder $query;

    public function __construct()
    {
        /** @var Builder<PpeDelivery> $query */
        $query = PpeDelivery::query()->with(['user', 'employee']);
        $this->query = $query;
    }

    public static function make(): self
    {
        return new self();
    }

    public function apply(array $filters, array $sorts): self
    {
        foreach ($filters as $field => $value) {
            if (blank($value)) continue;
            match ((string) $field) {
                'id'       => $this->query->where('id', (int) $value),
                'employee' => $this->query->whereHas('employee', fn($q) => $q->where('name', 'ilike', "%$value%")),
                'user'     => $this->query->whereHas('user', fn($q) => $q->where('username', 'ilike', "%$value%")),
                'date'     => $this->query->whereDate('created_at', $value),
                default    => $this->query->where((string) $field, 'ilike', "%$value%"),
            };
        }
        foreach ($sorts as $field => $dir) {
            $this->query->orderBy((string) $field, $dir);
        }
        if (empty($sorts)) $this->query->orderByDesc('id');
        return $this;
    }

    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        return $this->query->paginate($size, ['*'], 'page', $page);
    }
}
