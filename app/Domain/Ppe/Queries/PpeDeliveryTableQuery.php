<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Queries;

use App\Domain\Ppe\Models\PpeDelivery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class PpeDeliveryTableQuery
{
    /** @var Builder<PpeDelivery> */
    public private(set) Builder $query;

    public function __construct()
    {
        /** @var Builder<PpeDelivery> $q */
        $q = PpeDelivery::query()->select(
            'epp.*',
            DB::raw("(SELECT username FROM users WHERE id = epp.user_id LIMIT 1) as user_name"),
            DB::raw("(SELECT name FROM employees WHERE id = epp.employee_id LIMIT 1) as employee_name"),
            DB::raw("(SELECT area FROM hr_db WHERE id = (SELECT profile FROM employees WHERE id = epp.employee_id LIMIT 1) LIMIT 1) as area_name"),
        );
        $this->query = $q;
    }

    public static function make(): self { return new self; }

    /** @param array<string,mixed> $filters @param array<string,string> $sorts */
    public function apply(array $filters, array $sorts): self
    {
        foreach ($filters as $field => $value) {
            if (blank($value)) continue;
            $val = (string) $value;
            match ((string) $field) {
                'id'       => $this->query->where('epp.id', 'ilike', "%{$val}%"),
                'name'     => $this->query->where('epp.name', 'ilike', "%{$val}%"),
                'type'     => $this->query->where('epp.kind', 'ilike', "%{$val}%"),
                'notes'    => $this->query->where('epp.notes', 'ilike', "%{$val}%"),
                'employee' => $this->query->whereRaw(
                    "EXISTS (SELECT 1 FROM employees WHERE id = epp.employee_id AND name ILIKE ?)", ["%{$val}%"]
                ),
                'date'     => str_contains($val, ' to ')
                    ? $this->filterDateRange('epp.created_at', $val)
                    : $this->query->whereRaw("DATE(epp.created_at) = ?", [$val]),
                default    => null,
            };
        }
        if (empty($sorts)) {
            $this->query->orderBy('epp.id', 'desc');
        } else {
            foreach ($sorts as $field => $dir) {
                $dir = strtolower($dir) === 'asc' ? 'asc' : 'desc';
                match ((string) $field) {
                    'id'   => $this->query->orderBy('epp.id', $dir),
                    'date' => $this->query->orderBy('epp.created_at', $dir),
                    'name' => $this->query->orderBy('epp.name', $dir),
                    default => null,
                };
            }
        }
        return $this;
    }

    private function filterDateRange(string $col, string $val): void
    {
        [$from, $to] = explode(' to ', $val);
        $this->query->whereBetween(DB::raw("DATE({$col})"), [trim($from), trim($to)]);
    }

    /** @return LengthAwarePaginator<PpeDelivery> */
    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        return $this->query->paginate($size, ['*'], 'page', $page);
    }
}
