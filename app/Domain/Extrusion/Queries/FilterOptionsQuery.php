<?php

declare(strict_types=1);

namespace App\Domain\Extrusion\Queries;

use Illuminate\Support\Facades\DB;

final class FilterOptionsQuery
{
    /** @var array<string,string> */
    private const FIELDS = [
        'company'  => 'company_id',
        'category' => 'category_id',
        'b'        => 'b',
        'h'        => 'h',
        'e1'       => 'e1',
        'e2'       => 'e2',
    ];

    /**
     * Return distinct values for each dimension field, optionally scoped
     * by active filters on the *other* fields (relational cascade).
     *
     * @param  array<string,string> $activeFilters  field=>value already set by user
     * @return array<string,list<string>>
     */
    public function get(array $activeFilters = []): array
    {
        $result = [];

        foreach (self::FIELDS as $key => $col) {
            $query = DB::table('matrices')
                ->selectRaw("DISTINCT TRIM(REPLACE(CAST({$col} AS TEXT), '\u00a0', ' ')) as val")
                ->whereNotNull($col)
                ->where($col, '!=', '');

            // Apply all active filters *except* for the field we're building options for
            foreach ($activeFilters as $fKey => $fVal) {
                if ($fKey === $key || blank($fVal)) {
                    continue;
                }
                $dbCol = self::FIELDS[$fKey] ?? null;
                if ($dbCol === null) {
                    continue;
                }
                $query->whereRaw(
                    "TRIM(REPLACE(CAST({$dbCol} AS TEXT), '\u00a0', ' ')) = ?",
                    [(string) $fVal]
                );
            }

            $rows = $query->orderBy('val')->get();

            $result[$key] = $rows
                ->map(fn ($r) => trim((string) $r->val))
                ->filter(fn ($v) => $v !== '')
                ->unique()
                ->values()
                ->all();
        }

        return $result;
    }
}
