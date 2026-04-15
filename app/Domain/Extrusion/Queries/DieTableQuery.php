<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Queries;

use App\Domain\Extrusion\Models\Die;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class DieTableQuery
{
    /** @var Builder<Die> */
    public private(set) Builder $query;

    // Fields that require exact match (not ILIKE)
    private const EXACT = ['company_id', 'category_id', 'b', 'h', 'e1', 'e2'];

    public function __construct()
    {
        $this->query = Die::query();
    }

    public static function make(): self { return new self; }

    /**
     * @param array<string,mixed>  $filters
     * @param array<string,string> $sorts
     */
    public function apply(array $filters, array $sorts): self
    {
        foreach ($filters as $field => $value) {
            if (blank($value)) {
                continue;
            }
            $val = (string) $value;
            match ($field) {
                'geometry_shape' => $this->query->where('geometry_shape', 'ilike', "%{$val}%"),
                'company'        => $this->query->where('company_id', $val),
                'category'       => $this->query->where('category_id', $val),
                'b'              => $this->query->where('b', $val),
                'h'              => $this->query->where('h', $val),
                'e1'             => $this->query->where('e1', $val),
                'e2'             => $this->query->where('e2', $val),
                'clicks'         => $this->query->where('clicks', 'ilike', "%{$val}%"),
                'system'         => $this->query->where('systema', 'ilike', "%{$val}%"),
                default          => null,
            };
        }

        if (empty($sorts)) {
            $this->query->orderBy('id', 'desc');
        } else {
            foreach ($sorts as $field => $dir) {
                $dir = strtolower($dir) === 'asc' ? 'asc' : 'desc';
                match ($field) {
                    'id', 'geometry_shape', 'company', 'category', 'b', 'h', 'e1', 'e2'
                        => $this->query->orderBy($field, $dir),
                    default => null,
                };
            }
        }

        return $this;
    }

    /** @return LengthAwarePaginator<Die> */
    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        return $this->query->paginate($size, ['*'], 'page', $page);
    }
}
