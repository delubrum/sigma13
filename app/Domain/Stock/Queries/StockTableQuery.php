<?php

declare(strict_types=1);

namespace App\Domain\Stock\Queries;

use App\Domain\Stock\Models\Stock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class StockTableQuery
{
    /** @var Builder<Stock> */
    public private(set) Builder $query;

    public function __construct()
    {
        /** @var Builder<Stock> $query */
        $query = Stock::query()->select('stock.*');

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
                'id'         => $this->query->where('stock.id', (int) $value),
                'kind'       => $this->query->where('stock.kind', $valStr),
                'name'       => $this->query->where('stock.name', 'ilike', '%' . $valStr . '%'),
                'code'       => $this->query->where('stock.code', 'ilike', '%' . $valStr . '%'),
                'area'       => $this->query->where('stock.area', 'ilike', '%' . $valStr . '%'),
                'created_at' => $this->applyDateFilter($valStr),
                'search'     => $this->query->where(function (Builder $q) use ($valStr) {
                    $q->where('stock.name', 'ilike', '%' . $valStr . '%')
                        ->orWhere('stock.kind', 'ilike', '%' . $valStr . '%')
                        ->orWhere('stock.area', 'ilike', '%' . $valStr . '%')
                        ->orWhereRaw('CAST(stock.code AS TEXT) LIKE ?', ['%' . $valStr . '%'])
                        ->orWhereRaw('CAST(stock.id AS TEXT) LIKE ?', ['%' . $valStr . '%']);
                }),
                default => $this->query->where('stock.' . (string) $field, 'ilike', '%' . $valStr . '%'),
            };
        }

        return $this;
    }

    /** @param array<string, string> $sorts */
    private function sort(array $sorts): self
    {
        $allowed = ['id', 'name', 'kind', 'code', 'min_stock', 'initial_stock', 'area', 'created_at'];

        if ($sorts === []) {
            $this->query->orderBy('stock.id', 'DESC');
            return $this;
        }

        foreach ($sorts as $field => $dir) {
            if (in_array($field, $allowed, true)) {
                $this->query->orderBy('stock.' . (string) $field, strtoupper($dir));
            }
        }

        return $this;
    }

    /** @return LengthAwarePaginator<int, Stock> */
    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        /** @var LengthAwarePaginator<int, Stock> $paginator */
        $paginator = $this->query->paginate($size, ['*'], 'page', $page);

        return $paginator;
    }

    private function applyDateFilter(string $value): void
    {
        if (str_contains($value, ' to ')) {
            [$from, $to] = explode(' to ', $value);
            $this->query->whereBetween('stock.created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        } else {
            $this->query->whereDate('stock.created_at', $value);
        }
    }
}
