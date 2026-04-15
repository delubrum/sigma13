<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Queries;

use App\Domain\Ppe\Models\PpeItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class PpeItemTableQuery
{
    /** @var Builder<PpeItem> */
    public private(set) Builder $query;

    public function __construct()
    {
        $this->query = PpeItem::query();
    }

    public static function make(): self { return new self; }

    /** @param array<string,mixed> $filters @param array<string,string> $sorts */
    public function apply(array $filters, array $sorts): self
    {
        foreach ($filters as $field => $value) {
            if (blank($value)) continue;
            $val = (string) $value;
            match ((string) $field) {
                'id'   => $this->query->where('id', 'ilike', "%{$val}%"),
                'name' => $this->query->where('name', 'ilike', "%{$val}%"),
                'code' => $this->query->where('code', 'ilike', "%{$val}%"),
                default => null,
            };
        }
        if (empty($sorts)) {
            $this->query->orderBy('id', 'desc');
        } else {
            foreach ($sorts as $field => $dir) {
                $dir = strtolower($dir) === 'asc' ? 'asc' : 'desc';
                match ((string) $field) {
                    'id', 'name', 'code', 'price' => $this->query->orderBy($field, $dir),
                    default => null,
                };
            }
        }
        return $this;
    }

    /** @return LengthAwarePaginator<PpeItem> */
    public function paginate(int $page, int $size): LengthAwarePaginator
    {
        return $this->query->paginate($size, ['*'], 'page', $page);
    }
}
