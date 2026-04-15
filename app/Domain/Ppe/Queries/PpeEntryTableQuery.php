<?php

declare(strict_types=1);

namespace App\Domain\Ppe\Queries;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class PpeEntryTableQuery
{
    public static function make(): self { return new self; }

    /**
     * @param array<string,mixed>  $filters
     * @param array<string,string> $sorts
     * @return LengthAwarePaginator<object>
     */
    public function paginate(array $filters, array $sorts, int $page, int $size): LengthAwarePaginator
    {
        $query = DB::table('epp_db as b')
            ->select(
                'b.id as item_id',
                'b.name',
                DB::raw("COALESCE((SELECT SUM(r.qty) FROM epp_register r WHERE r.item_id = b.id), 0)
                        - COALESCE((SELECT COUNT(e.id) FROM epp e WHERE e.name = b.name), 0) as stock")
            )
            ->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('epp_register as r')
                    ->whereColumn('r.item_id', 'b.id');
            });

        foreach ($filters as $field => $value) {
            if (blank($value)) {
                continue;
            }
            $val = (string) $value;
            match ((string) $field) {
                'id'   => $query->where('b.id', 'ilike', "%{$val}%"),
                'name' => $query->where('b.name', 'ilike', "%{$val}%"),
                default => null,
            };
        }

        if (empty($sorts)) {
            $query->orderBy('b.name', 'asc');
        } else {
            foreach ($sorts as $field => $dir) {
                $dir = strtolower($dir) === 'asc' ? 'asc' : 'desc';
                match ((string) $field) {
                    'id'   => $query->orderBy('b.id', $dir),
                    'name' => $query->orderBy('b.name', $dir),
                    default => null,
                };
            }
        }

        return $query->paginate($size, ['*'], 'page', $page);
    }
}
